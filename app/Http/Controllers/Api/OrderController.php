<?php

namespace App\Http\Controllers\Api;

use App\Events\InvoiceUpdated;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PDF;

class OrderController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'customer_id'       => ['required','integer','exists:customers,id'],
            'thaila_price'      => ['nullable','numeric'],
            'is_round_off'      => ['required','boolean'],
            'round_off'  => ['nullable','numeric'],
            'sub_total'         => ['required','numeric'],
            'grand_total'       => ['required','numeric'],
            'products'                  => ['required','array'],
            'products.*.product_id'     => ['required','exists:products,id'],
            'products.*.quantity'       => ['required','numeric','min:0.00001'],
            'products.*.price'          => ['required','numeric'],
            'products.*.total_price'    => ['required','numeric'],
        ]);
        if($validator->fails()){
            $responseData = [
                'status'        => false,
                'validation_errors' => $validator->errors(),
            ];
            return response()->json($responseData, 401);
        }
        try {
            DB::beginTransaction();
                // Create a new order
                $orderRequest = $request->except('products');
                $orderRequest['invoice_date'] = Carbon::now();
                $order = Order::create($orderRequest);
                // Generate the invoice number
                $invoiceNumber = generateInvoiceNumber($order->id);
                $order->update(['invoice_number' => $invoiceNumber]);
                // Create order products
                $productItem = $request->products;
                $order->orderProduct()->createMany($productItem);
            DB::commit();
            // Fire the InvoiceUpdated event after successful creation
            dispatchInvoiceUpdatedEvent();
            $responseData = [
                'status'        => true,
                'message' => trans('messages.success'),
            ];
            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            $responseData = [
                'status'        => false,
                'error'         => $e->getMessage(),
            ];
            return response()->json($responseData, 500);
        }
    }

    public function update(Request $request, Order $order)
    {
       //dd($request->all());
       $validator = Validator::make($request->all(),[
        'customer_id' => ['required','integer'],
        'thaila_price' => ['nullable','numeric'],
        'is_round_off' => ['required','boolean'],
        'round_off' => ['nullable','numeric'],
        'sub_total'=> ['required','numeric'],
        'grand_total' => ['required','numeric'],
        'products' => ['required','array'],
        // 'products.*.order_product_id' => ['required','exists:order_products,id'],
        'products.*.product_id' => ['required','exists:products,id'],
        'products.*.quantity' => ['required','numeric','min:0.00001'],
        'products.*.price' => ['required','numeric'],
        'products.*.total_price' => ['required','numeric'],
        ]);

        if($validator->fails()){
            $responseData = [
                'status'        => false,
                'validation_errors' => $validator->errors(),
            ];
            return response()->json($responseData, 401);
        }

        try {
            DB::beginTransaction();
            $order->update([
                'customer_id' => $request->customer_id,
                'thaila_price' => $request->thaila_price,
                'round_off' => $request->round_off,
                'is_round_off' => $request->is_round_off,
                'sub_total' => $request->sub_total,
                'grand_total' => $request->grand_total,
            ]);

            $existingOrderProductIds = $order->orderProduct->pluck('id')->toArray();
            $updatedOrderProductIds = collect($request->products)->pluck('id')->toArray();
            $deletedOrderProductIds = array_diff($existingOrderProductIds, $updatedOrderProductIds); // it will return the ids from 1st array that is not present the 2nd array
            OrderProduct::whereIn('id', $deletedOrderProductIds)->delete();   // Delete existing order products that are not in the updated list
            // Update or create order products
            foreach ($request->products as $productData) {
                $productId = $productData['id'] ?? null;
                OrderProduct::updateOrCreate(
                    ['id' => $productId],
                    [
                        'order_id' => $order->id,
                        'product_id' => $productData['product_id'],
                        'quantity' => $productData['quantity'],
                        'price' => $productData['price'],
                        'total_price' => $productData['total_price'],
                    ]
                );
            }

            DB::commit();
            // Fire the InvoiceUpdated event after successful creation
            dispatchInvoiceUpdatedEvent();
            $responseData = [
                'status'        => true,
                'message' => trans('messages.success'),
            ];
            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            //dd($e->getMessage());
            DB::rollBack();
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 500);
        }
    }

    public function destroy(Order $order)
    {
        //dd($order);
        if (!$order) {
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.order_not_found'),
            ];
            return response()->json($responseData, 404);
        }

        try {
            DB::beginTransaction();
            $order->delete();
            DB::commit();

            $responseData = [
                'status'        => true,
                'message' => trans('messages.success'),
            ];
            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 500);
        }
    }


    public function generateInvoicePdf($orderId, $type = null)
    {
        try {
            $order = Order::with('orderProduct.product')->findOrFail($orderId);
            $pdf = PDF::loadView('admin.order.pdf.invoice-pdf', compact('order', 'type'));
            $pdf->getMpdf()->setFooter('Page {PAGENO}');
            //return $pdf->download('order_' . $order->invoice_number . '.pdf');
            //return $pdf->stream('order_' . $order->invoice_number . '.pdf');
            $pdfContent = $pdf->output();
            $base64Pdf = base64_encode($pdfContent);

            return response()->json([
                'status'        => true,
                'pdf' => $base64Pdf,
            ]);
        } catch (\Exception $e) {
            //dd($e->getMessage());
            return response()->json(['error' => 'Error generating PDF'], 500);
        }
    }


    public function generatePartyAllInvoicePdf($customerId)
    {
        try {
            $today = Carbon::today();
            $type = null;
            $orders = Order::with(['customer.address', 'orderProduct.product'])->whereDate('created_at', $today)
            ->where('customer_id', $customerId)
            ->orderBy('updated_at','desc')
            ->get();

            $pdfs = [];

            foreach($orders as $order){
                $pdf = PDF::loadView('admin.order.pdf.invoice-pdf', compact('order', 'type'));
               // $pdf->setPaper('A4', 'portrait');
               $pdf->getMpdf()->setFooter('Page {PAGENO}');
                $pdfContent = $pdf->output();
                $base64Pdf = base64_encode($pdfContent);
                $pdfs[] = [
                    'order_id' => $order->id,
                    'pdf'      => $base64Pdf,
                ];
            }

            return response()->json([
                'status' => true,
                'pdfs'   => $pdfs,
            ]);

        } catch (\Exception $e) {
            //dd($e->getMessage());
            return response()->json(['error' => 'Error generating PDF'], 500);
        }
    }






}
