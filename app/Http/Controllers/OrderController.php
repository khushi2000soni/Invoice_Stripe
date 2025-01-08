<?php

namespace App\Http\Controllers;

use App\DataTables\InvoiceDataTable;
use App\DataTables\InvoiceTypeDataTable;
use App\Events\InvoiceUpdated;
use App\Exports\InvoiceExport;
use App\Http\Requests\Order\StoreRequest;
use App\Http\Requests\Order\UpdateRequest;
use App\Mail\ShareInvoiceMail;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Traits\InvoiceNumberGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class OrderController extends Controller
{
    use InvoiceNumberGenerator;
    public function index(InvoiceDataTable $dataTable)
    {
        abort_if(Gate::denies('invoice_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $customers = Customer::all();
        $type='all';
        return $dataTable->render('admin.order.index',compact('customers','type'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        $invoice_date = Carbon::now()->format('d-m-Y');
        return view('admin.order.create',compact('customers','products','invoice_date'));
    }

    public function store(StoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $order = Order::create([
                'customer_id' => (int)$request->customer_id,
                'thaila_price' => (float)$request->thaila_price,
                'is_round_off' => $request->is_round_off,
                'sub_total' => (float)$request->sub_total,
                'round_off' => (float)$request->round_off,
                'grand_total' => (float)$request->grand_total,
                'invoice_date' => Carbon::now(),
            ]);            

            $invoiceNumber = $this->generateUniqueInvoiceNumber($order->id);
            $order->update(['invoice_number' => $invoiceNumber]);
            
            foreach ($request->products as $productData) {
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'price' => $productData['price'],
                    'total_price' => $productData['total_price'],
                ]);
            }
            Log::info($invoiceNumber."  Order created successfully.");
            DB::commit();
            dispatchInvoiceUpdatedEvent();
            return response()->json(['success' => true,
            'message' => trans('messages.crud.add_record'),
            'alert-type'=> trans('quickadmin.alert-type.success')], 200);

        }catch (QueryException $e) {
           
            if ($e->getCode() === '23000') { 
                $invoiceNumber = $this->generateUniqueInvoiceNumber($order->id);
                $order->update(['invoice_number' => $invoiceNumber]);   
                DB::commit(); 
    
                Log::error("Failed to Add Invoice:-". $e->getMessage()." in " . $e->getCode(). " at line" . $e->getLine());
                return response()->json(['success' => false,
                'message' => trans('messages.error1'),
                'alert-type'=> trans('quickadmin.alert-type.error')], 500);             
            }    
            // Rollback transaction for any other database-related exceptions
            DB::rollBack();
            Log::error("Failed to Add Invoice:-". $e->getMessage()." in " . $e->getCode(). " at line" . $e->getLine());
            return response()->json(['success' => false,
            'message' => 'Database error: ' . $e->getMessage(),
            'alert-type'=> trans('quickadmin.alert-type.error')], 500);
    
        } catch (\Exception $e) {
           //dd($e->getMessage());
            DB::rollBack();
            Log::error("Failed to Add Invoice:-". $e->getMessage()." in " . $e->getCode(). " at line" . $e->getLine());
            return response()->json(['success' => false,
            'message' => trans('messages.error1'),
            'alert-type'=> trans('quickadmin.alert-type.error')], 500);
        }
    }


    public function show(int $id)
    {
        $order = Order::withTrashed()->with('orderProduct.product')->findOrFail($id);
        $htmlView = view('admin.order.show', compact('order'))->render();
        return response()->json(['success' => true, 'htmlView' => $htmlView]);
    }

    public function edit($id)
    {
        $order = Order::with('orderProduct.product')->findOrFail($id);
        $customer_id = $order->customer->id;
        $products = $order->orderProduct->map(function ($product) use ($customer_id) {
        $associatedProduct = Product::find($product->product_id);
            return [
                'order_product_id'=>$product->id,
                // 'customer_id' => $customer_id,
                'product_id' => $product->product_id,
                'product_name' => $associatedProduct->name,
                'quantity' => $product->quantity,
                'price' => $product->price,
                'total_price' => $product->total_price,
            ];
        })->toArray();

        $orderData = [
            'orderid' => $order->id,
            'invoice_number' => $order->invoice_number,
            'customer_id' => $customer_id,
            'products' => $products,
            'thailaPrice' => $order->thaila_price,
            'is_round_off' => $order->is_round_off,
            'sub_total' => $order->sub_total,
            'round_off_amount' => $order->round_off,
            'grand_total' => $order->grand_total,
        ];

        $invoice_date = $order->invoice_date ? Carbon::parse($order->invoice_date)->format('d-m-Y') : null;

        //dd($orderData);

        $products = Product::all();
        $customers = Customer::all();
        //dd($customers);
        return view('admin.order.edit',compact('order','invoice_date','customers','products','orderData'));
    }


    public function update(UpdateRequest $request, Order $order)
    {
        try {
            DB::beginTransaction();
            $order->update([
                'customer_id' => $request->customer_id,
                'thaila_price' => $request->thaila_price,
                'is_round_off' => $request->is_round_off,
                'sub_total' => $request->sub_total,
                'round_off' => $request->round_off,
                'grand_total' => $request->grand_total,
            ]);

            $productsToDelete = $request->deleted_products ?? [];
            foreach ($request->products as $productData) {
                if (isset($productData['order_product_id']) && !empty($productData['order_product_id'])) {
                    // Update existing product with order_product_id
                    $existingProduct = OrderProduct::find($productData['order_product_id']);
                    if ($existingProduct) {
                        $existingProduct->update([
                            'quantity' => $productData['quantity'],
                            'product_id' => $productData['product_id'],
                            'price' => $productData['price'],
                            'total_price' => $productData['total_price'],
                        ]);
                    }
                } else {
                    // Create a new product without order_product_id
                    OrderProduct::create([
                        'order_id' => $order->id,
                        'product_id' => $productData['product_id'],
                        'quantity' => $productData['quantity'],
                        'price' => $productData['price'],
                        'total_price' => $productData['total_price'],
                    ]);
                }
            }

            if (!empty($productsToDelete)) {
                OrderProduct::whereIn('id', $productsToDelete)->delete();
            }

            DB::commit();
            dispatchInvoiceUpdatedEvent();
            return response()->json([
                'success' => true,
                'message' => trans('messages.crud.update_record'),
                'alert-type' => trans('quickadmin.alert-type.success')
            ], 200);
        } catch (\Exception $e) {
            //dd($e->getMessage());
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => trans('messages.error1'),
                'alert-type' => trans('quickadmin.alert-type.error')
            ], 500);
        }
    }

    public function getTypeOrder(InvoiceTypeDataTable $dataTable,string $type){
        abort_if(Gate::denies('invoice_recycle_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $customers = Customer::all();
        return $dataTable->render('admin.order.index',compact('customers','type'));
    }

    public function printView($order,$type=null)
    {
        abort_if(Gate::denies('invoice_print'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $pdfConfig = config('pdf');
            if($type=='deleted'){
                $order = Order::withTrashed()
                ->with(['orderProduct' => function ($query) {
                    $query->withTrashed()->with('product');
                    $pdfConfig['show_watermark_image'] = true;
                    $pdfConfig['watermark_image_path'] = public_path('admintheme/assets/img/cancelled.png');
                }])
                ->findOrFail($order);
            }else{
                $order = Order::withTrashed()->with('orderProduct.product')->findOrFail($order);
                $pdfConfig['show_watermark_image'] = false;
            }

            config(['pdf' => $pdfConfig]);
            $pdf = PDF::loadView('admin.order.pdf.invoice-pdf', compact('order', 'type'));
            $pdf->getMpdf()->setFooter('Page {PAGENO}');
            $pdfContent = $pdf->output();
            $base64Pdf = base64_encode($pdfContent);
            return response()->json([
                'status'        => true,
                'pdf' => $base64Pdf,
            ]);
        } catch (\Exception $e) {
           // dd($e->getMessage());
            return response()->json(['error' => 'Error generating PDF'], 500);
        }
    }

    public function orderdetailprint($order,$type=null){
        abort_if(Gate::denies('invoice_print'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $pdfConfig = config('pdf');
            if($type=='deleted'){
                $order = Order::withTrashed()
                ->with(['orderProduct' => function ($query) {
                    $query->withTrashed()->with('product');
                    $pdfConfig['show_watermark_image'] = true;
                    $pdfConfig['watermark_image_path'] = public_path('admintheme/assets/img/cancelled.png');
                }])
                ->findOrFail($order);
            }else{
                $order = Order::withTrashed()->with('orderProduct.product')->findOrFail($order);
                $pdfConfig['show_watermark_image'] = false;
            }

            $htmlView = view('admin.order.pdf.invoice-print',compact('order', 'type'))->render();
            return response()->json([
                'status'        => true,
                'pdf' => $htmlView,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error generating PDF'], 500);
        }
    }

    public function generatePdf($orderId,$type=null)
    {
        //dd($orderId);
        $order = Order::with('orderProduct.product')->findOrFail($orderId);
        $pdf = PDF::loadView('admin.order.pdf.invoice-pdf', compact('order','type'));
       // $pdf->setPaper('A4', 'portrait');
       $customer_name = $order->customer->full_name;
       $pdfFileName = $order->invoice_number.'_'.$customer_name . '.pdf';
        return $pdf->download($pdfFileName);
    }

    public function printPDF(Request $request, $order,$type=null)
    {
        $pdfConfig = config('pdf');
        if($type=='deleted'){
            $order = Order::withTrashed()
            ->with(['orderProduct' => function ($query) {
                $query->withTrashed()->with('product');
            }])
            ->findOrFail($order);

            $pdfConfig['show_watermark_image'] = true;
            $pdfConfig['watermark_image_path'] = public_path('admintheme/assets/img/cancelled.png');
        }else{
            $order = Order::withTrashed()->with('orderProduct.product')->findOrFail($order);

            $pdfConfig['show_watermark_image'] = false;
        }

        config(['pdf' => $pdfConfig]);
        $customer_name = $order->customer->full_name;
        $pdfFileName = $order->invoice_number.'_'.$customer_name . '.pdf';
        $pdf = PDF::loadView('admin.order.pdf.invoice-pdf', compact('order','type'));
        $pdf->getMpdf()->setFooter('Page {PAGENO}');
        $pdf->getMpdf()->SetFont('Mangal', 'B');
        return $pdf->stream($pdfFileName, ['Attachment' => false]);
        // return view('admin.order.pdf.invoice-pdf', compact('order','type'));
    }

    /// Render Share mail modal form
    public function shareEmail(Request $request, Order $order)
    {
        $htmlView = view('admin.order.share_email_modal', compact('order'))->render();
        return response()->json(['success' => true, 'htmlView' => $htmlView]);
    }

    // Send mail to request recipient
    public function sendshareEmail(Request $request , Order $order)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);
        $type=null;
        try{
            $pdfData = generateInvoicePdf($order->id,$type);
            $pdfContent = $pdfData['pdfContent'];
            $pdfFileName = $pdfData['pdfFileName'];
            $order = Order::with('orderProduct.product')->findOrFail($order->id);
            $recipientEmail = $request->email;
            Mail::to($recipientEmail)->send(new ShareInvoiceMail($pdfContent, $pdfFileName , $order->customer->name));
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.invoice_mail'),
                    'alert-type' => trans('quickadmin.alert-type.success')
                ], 200);

        }catch(\Exception $e){
            //dd($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => trans('messages.error1'),
                'alert-type' => trans('quickadmin.alert-type.success')
            ], 200);
        }
    }

    public function shareWhatsApp(Request $request, $order)
    {
        //dd($request->all());
        $url = 'https://web.whatsapp.com/send?text=' . urlencode('Check out this invoice: ' . route('orders.generate-pdf', $order->id));
        return redirect($url);
    }

    public function allinvoicePrintView(Request $request)
    {
        abort_if(Gate::denies('invoice_print'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $customer_id = $request->customer_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $query = Order::query();
        if ($customer_id !== null && $customer_id != 'null') {
            $customer = Customer::find($customer_id);
            $customer_name = $customer ? $customer->name : 'Unknown Party';
            $query->where('customer_id','=', $customer_id);
        } else {
            $customer_name = 'All Parties';
        }

        if ($from_date !== null && $from_date != 'null') {
            $query->whereDate('invoice_date','>=', $from_date);
        }
        if ($to_date !== null && $to_date != 'null') {
            $query->whereDate('invoice_date','<=',  $to_date);
        }

        if (!(auth()->user()->hasRole(config('app.roleid.super_admin')))) {
            $days = getSetting('invoice_allow_day_admin_accountant');
            $query = $query->whereDate('invoice_date', '>=', now()->subDays($days));
        }

        $from_date = $from_date ? Carbon::parse($from_date)->format('d-m-Y') : null;
        $to_date = $to_date ? Carbon::parse($to_date)->format('d-m-Y') : null;
        $duration = ($from_date && $to_date) ? $from_date . ' - ' . $to_date : 'All Time';
        $allorders = $query->orderBy('id','desc')->get();

        $sumGrandTotal = $allorders->sum('grand_total');
        return view('admin.order.print-orders-list',compact('allorders','sumGrandTotal','duration','customer_name'))->render();
    }

    public function allinvoiceExport(Request $request){
        $customer_id = $request->input('customer_id');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        abort_if(Gate::denies('invoice_share'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return Excel::download(new InvoiceExport($customer_id,$from_date,$to_date), 'Invoice-List.xlsx');
    }


    public function destroy($id , $type=null)
    {
        abort_if(Gate::denies('invoice_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            DB::beginTransaction();
            $order = Order::withTrashed()->findOrFail($id);
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                    'alert-type' => trans('quickadmin.alert-type.error')
                ], 404);
            }

            if ($type == 'deleted') {
                // Permanently delete the record
                $order->orderProduct()->forceDelete();
                $order->forceDelete();
            } else {
                // Soft delete the record
                $order->delete();
            }

            DB::commit();
            return response()->json(['success' => true,
            'message' => trans('messages.crud.delete_record'),
            'alert-type'=> trans('quickadmin.alert-type.success'),
            'title' => trans('quickadmin.order.invoice')
            ], 200);
        }
        catch (\Exception $e) {
            //dd($e->getMessage());
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => trans('messages.error_message'),
                'alert-type' => trans('quickadmin.alert-type.error')
            ], 500);
        }
    }

    public function restore($id){
        abort_if(Gate::denies('invoice_restore'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            DB::beginTransaction();
            $order = Order::withTrashed()->findOrFail($id);
            $order->restore();
            DB::commit();
            return response()->json(['success' => true,
            'message' => trans('messages.crud.restore_record'),
            'alert-type'=> trans('quickadmin.alert-type.success'),
            'title' => trans('quickadmin.order.invoice')
            ], 200);
        }
        catch (\Exception $e) {
            //dd($e->getMessage());
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => trans('messages.error_message'),
                'alert-type' => trans('quickadmin.alert-type.error')
            ], 500);
        }
    }

    // Checkout Functionality

    public function showCheckoutDetail($id)
    {
        $order = Order::with('customer', 'orderProduct.product')->findOrFail($id); // Eager load related data
        return view('admin.order.checkout', compact('order'));
    }
}
