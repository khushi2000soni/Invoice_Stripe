<?php

namespace App\Http\Controllers;

use App\DataTables\CustomerDataTable;
use App\DataTables\PhoneBookDataTable;
use App\Exports\CustomerExport;
use App\Exports\PhoneBookExport;
use App\Http\Requests\Customer\CreateRequest;
use App\Http\Requests\Customer\UpdateRequest;
use App\Models\Address;
use App\Models\Customer;
use App\Rules\UniquePhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function index(CustomerDataTable $dataTable)
    {
        abort_if(Gate::denies('customer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $addresses = Address::orderByRaw('CAST(address AS SIGNED), address')->get();
        return $dataTable->render('admin.customer.index',compact('addresses'));
    }

    public function printView($address_id = null)
    {
        abort_if(Gate::denies('customer_print'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $query = Customer::query();
        if ($address_id !== null) {
            $query->where('address_id', $address_id);
        }
        $customers = $query->orderBy('id','desc')->get();
        return view('admin.customer.print-customer-list',compact('customers'))->render();
    }

    public function export($address_id = null){
        abort_if(Gate::denies('customer_export'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $filename = $address_id ? 'Parties-' . Address::find($address_id)->address : 'Parties-all';
        return Excel::download(new CustomerExport($address_id), $filename.'.xlsx');
    }

    public function create()
    {
        $addresses = Address::orderByRaw('CAST(address AS SIGNED), address')->get();
        $htmlView = view('admin.customer.create', compact('addresses'))->render();
        return response()->json(['success' => true, 'htmlView' => $htmlView]);
    }

    public function store(CreateRequest $request)
    {
        $input = $request->all();
        if ((auth()->user()->hasRole(config('app.roleid.super_admin')))) {
            $input['is_verified']=true;
        }

        $customer=Customer::create($input);
        return response()->json(['success' => true,
        'message' => trans('messages.crud.add_record'),
        'alert-type'=> trans('quickadmin.alert-type.success'),
        'title' => trans('quickadmin.customers.customer'),
        'selectdata' => [
            'id' => $customer->id,
            'name' => $customer->full_name,
            'formtype' => 'customer',
            ],
        ], 200);
    }

    public function edit($id)
    {
        $customer = Customer::with('address')->findOrFail($id);
        $addresses =   Address::orderByRaw('CAST(address AS SIGNED), address')->get();
        $htmlView = view('admin.customer.edit', compact('addresses','customer'))->render();
        return response()->json(['success' => true, 'htmlView' => $htmlView]);
    }

    public function update(UpdateRequest $request, Customer $customer)
    {
        $input = $request->all();
        $input['name']=ucwords($request->name);
        $customer->update($input);
        return response()->json(['success' => true,
        'message' => trans('messages.crud.update_record'),
        'alert-type'=> trans('quickadmin.alert-type.success')], 200);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('customer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $customer = Customer::with('orders.orderProduct')->findOrFail($id);
        foreach ($customer->orders as $order) {
            $order->orderProduct()->forceDelete();
            $order->forceDelete();
        }
        $customer->forceDelete();
        return response()->json([
            'success' => true,
            'message' => trans('messages.crud.delete_record'),
            'alert-type' => trans('quickadmin.alert-type.success'),
            'title' => trans('quickadmin.customers.customer'),
        ], 200);
    }

    //***************************Phone-Book Methods************************************** */

    public function showPhoneBook(PhoneBookDataTable $dataTable){
        //$addresses = Address::orderBy('address','asc')->get();
        $addresses = Address::orderByRaw('CAST(address AS SIGNED), address')->get();
        return $dataTable->render('admin.customer.phone-book',compact('addresses'));
    }

    public function PhoneBookprintView($address_id = null)
    {
        abort_if(Gate::denies('phone_book_print'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $query = Customer::query();
        if ($address_id !== null) {
            $query->where('address_id', $address_id);
        }
        $customers = $query->orderBy('name','asc')->get();
        return view('admin.customer.print-phone-book',compact('customers'))->render();
    }

    public function PhoneBookexport($address_id = null){
        abort_if(Gate::denies('phone_book_export'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return Excel::download(new PhoneBookExport($address_id), 'phone-book.xlsx');
    }
}
