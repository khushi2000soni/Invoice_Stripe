<?php

namespace App\Http\Controllers\Api;

use DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateRequest;
use App\Models\Address;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Rules\UniquePhoneNumber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function profileUpdate(Request $request)
    {
        $request->validate([
            'name'          => 'nullable|max:255',
            // 'phone'         => 'nullable|min:10|integer|unique:users,phone',
            'profile_image'   => 'nullable|image|max:2048|mimes:jpeg,png,gif',
            'address'    => 'nullable|string|max:255'
        ]);
        
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not authenticated',
            ], 401);
        }
        
        $user->update([
            'name'      => $request->name,
            'address'   => $request->address,
        ]);
    
        if ($request->hasFile('profile_image')) { 
            $actionType = 'save';
            $uploadId = null;
            if($profileImageRecord = $user->profileImage){
                $uploadId = $profileImageRecord->id;
                $actionType = 'update';
            }        
            $response = uploadImage($user, $request->profile_image, 'user/profile-images',"profile", 'original', $actionType, $uploadId);
        }    
        
        $responseData = [
            'status'            => true,
            'message'           => 'success',
        ];
        return response()->json($responseData, 200);
    }
    
    
    public function profile(Request $request){
        try{
            $user = auth()->user();
            $authData = [
                'id'            => $user->id,
                'name'          => $user->name ?? '',
                'username'      => $user->username ?? '',
                'email'         => $user->email ?? '',
                'phone'         => $user->phone ?? '',
                'address'       => $user->address ?? '',
                'profile_image' => $user->profile_image_url ?? '',
                'pin'           =>  $user->device? $user->device->pin : ''
            ];
        
            $responseData = [
                'status' => true,
                'message'   => 'Profile data retrive successfully!',
                'userData'  => $authData
            ];
                        
            return response()->json($responseData, 200);
            
        }catch (\Exception $e) {
            $responseData = [
                'status'        => false,
                'error'         => $e->getMessage().'->'.$e->getLine(),
            ];
            return response()->json($responseData, 401);
        }
    }
    
    
    public function todayInvoiceGroupList(){
        //$user= auth()->user();
        //$guardName = auth()->user()->guard_name;
        //$user = auth()->guard('sanctum')->user();
        //dd($user);
        // if (!$user->hasPermissionTo('customer_access')) {
        //     abort(403, 'Forbidden');
        // }
        // $permissions = $user->getAllPermissions();
        // dd($permissions);
        //abort_if(Gate::denies('customer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try{
            $today = Carbon::today();
            $customerOrders = Customer::select(
                'customers.id as customer_id',
                'customers.name as customer_name',
                'customers.address_id as address_id',
                'customers.guardian_name as guardian_name',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.grand_total) as total_order_amount'),
                'orders.invoice_date as invoice_date','latest_order.updated_at as updated_at',
                'address.address as city_name'
            )
            ->leftJoin('orders', function ($join) use ($today) {
                $join->on('customers.id', '=', 'orders.customer_id')
                    ->whereDate('orders.invoice_date', $today)
                    ->whereNull('orders.deleted_at');
            })
            ->leftJoin('address', 'customers.address_id', '=', 'address.id')
            ->leftJoin('orders as latest_order', function ($join) use ($today) {
                $join->on('customers.id', '=', 'latest_order.customer_id')
                    ->whereNull('latest_order.deleted_at')
                    ->where('latest_order.invoice_date', $today)
                    ->where('latest_order.updated_at', '=', function ($query) {
                        $query->select(DB::raw('MAX(updated_at)'))
                            ->from('orders')
                            ->whereRaw('customer_id = customers.id');
                    });
            })
            ->groupBy('customers.id', 'customers.name')
            ->havingRaw('total_orders > 0') // Exclude customers with no orders
            ->orderBy('latest_order.updated_at', 'desc')
            ->get()->toArray();

            foreach ($customerOrders as &$order) {
                //dd($order);
                $order['updated_at'] = getWithDateTimezone($order['updated_at']);
                //$order['created_at'] = Carbon::parse($order['created_at'])->format('d-m-Y H:i:s');
            }
            return response()->json([
                'status' => true,
                'data' => $customerOrders
            ])->setStatusCode(Response::HTTP_OK);
            // return response()->json($customerOrders, 200);
        }catch (\Exception $e) {
            dd($e->getMessage().'->'.$e->getLine());
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 401);
        }

    }

    public function PartyOrderDetail(Request $request){
        //dd($request->all());
        //abort_if(Gate::denies('customer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $customer = Customer::find($request->customer_id);
        if (!$customer) {
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.invalid_customer'),
            ];
            return response()->json($responseData, 401);
        }
        try{

            $today = Carbon::today();
            $orders = Order::with(['customer.address', 'orderProduct.product'])->whereDate('created_at', $today)
            ->where('customer_id', $request->customer_id)
            //->orderBy('updated_at','desc')
            ->get();

            //dd($orders);
            if ($orders->isNotEmpty()) {
                $responseData = [
                    'status' => true,
                    'message' => trans('messages.success'),
                    'customerData' => [
                        'customer_id'        => $customer->id ?? '',
                        'customer_name'   => $customer->name ?? '',
                        'customer_email'   => $customer->email ?? '',
                        'customer_phone1'   => $customer->phone ?? '',
                        'customer_phone2'   => $customer->phone2 ?? '',
                        'customer_address'   => $customer->address->address ?? '',
                        'allOrders' => [],
                    ],
                ];

                // Iterate through orders and build the response
                foreach ($orders as $order) {
                    $orderData = [
                        'order_id' => $order->id ?? '',
                        'invoice_number' => $order->invoice_number ?? '',
                        'invoice_date' => $order->invoice_date ?? '',
                        'thaila_price' => $order->thaila_price ?? '',
                        'is_round_off' => $order->is_round_off ?? '',
                        'round_off_amount' => $order->round_off_amount ?? '',
                        'sub_total' => $order->sub_total ?? '',
                        'grand_total' => $order->grand_total ?? '',
                        'updated_at' => $order->updated_at->format('d-m-Y H:i:s') ?? '',
                        'orderProducts' => [],
                    ];

                    // Iterate through order products and build the response
                    foreach ($order->orderProduct as $orderProduct) {
                        $orderData['orderProducts'][] = [
                            'order_product_id' => $orderProduct->id ?? '',
                            'product_id' => $orderProduct->product->id ?? '',
                            'product_name' => $orderProduct->product->name ?? '',
                            'quantity' => $orderProduct->quantity ?? '',
                            'price' => $orderProduct->price ?? '',
                            'total_price' => $orderProduct->total_price ?? '',
                        ];
                    }

                    $responseData['customerData']['allOrders'][] = ['orderData' => $orderData];
                }

                return response()->json($responseData, 200);
            } else {
                $responseData = [
                    'status'            => true,
                    'message'           => 'No Record for Today!',
                ];
                return response()->json($responseData, 200);
            }


        }catch (\Exception $e) {
            dd($e->getMessage().'->'.$e->getLine());
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 401);
        }

    }

    public function store(Request $request){

        $validator = Validator::make($request->all(),[
        'name' => ['required','string','max:150'/*,'regex:/^[^0-9\p{P}]+$/u'*/],
        'guardian_name' => ['nullable','string','max:150'/*,'regex:/^[^0-9\p{P}]+$/u'*/],
        // 'email' => ['required','email','unique:customers,email'],
        'phone' => ['nullable','digits:10','numeric',/*'unique:customers,phone'*/new UniquePhoneNumber($request->phone),],
        'phone2' => ['nullable','digits:10','numeric',/*'unique:customers,phone2'*/new UniquePhoneNumber($request->phone),],
        'city_id'=>['required','numeric'],
        ]);

        if($validator->fails()){
            $responseData = [
                'status'        => false,
                'validation_errors' => $validator->errors(),
            ];
            return response()->json($responseData, 401);
        }

        try{
            $input = $request->all();
            $input['name']=ucwords($request->name);
            $input['address_id']=ucwords($request->city_id);
            $input['guardian_name']=ucwords($request->guardian_name);
            $customer=Customer::create($input);
            $responseData = [
                'status'            => true,
                'message'           => trans('messages.success'),
                'userData'          => [
                    'customer_id'        => $customer->id ?? '',
                    'customer_name'   => $customer->name ?? '',
                    'customer_email'   => $customer->email ?? '',
                    'customer_phone1'   => $customer->phone ?? '',
                    'customer_phone2'   => $customer->phone2 ?? '',
                    'customer_address'   => $customer->address->address ?? '',
                ],
            ];
            return response()->json($responseData, 200);
        }catch (\Exception $e) {
            //dd($e->getMessage().'->'.$e->getLine());
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 401);
        }
    }

    public function PhoneValidation(Request $request){
        //dd($request->all());
        $validator = Validator::make($request->all(),[
           // 'phone' => ['numeric','digits:10','unique:customers,phone,phone2'],
           'phone' => ['numeric','digits:10',new UniquePhoneNumber($request->phone)],
        ]);

        if($validator->fails()){
            $responseData = [
                'status'        => false,
                'validation_errors' => $validator->errors(),
            ];
            return response()->json($responseData, 401);
        }

        $responseData = [
            'status'            => true,
            'message'           => trans('messages.success'),
        ];
        return response()->json($responseData, 200);
    }

    public function AllCustomerList(){
        $allcustomers = Customer::orderBy('id','desc')->get();

        $responseData = [
            'status'    => true,
            'message'   => trans('messages.success'),
            'customerData'  => [],
        ];
        foreach ($allcustomers as $customer) {
            $responseData['customerData'][] = [
                'customer_id'           => $customer->id ?? '',
                'customer_name'     => $customer->name ?? '',
                'city_name' => $customer->address? $customer->address->address : '',
                'guardian_name' => $customer->guardian_name ?? '',
                'phone' => $customer->phone ?? '',
                'phone2' => $customer->phone2 ?? '',
            ];
        }

        return response()->json($responseData, 200);
    }


}
