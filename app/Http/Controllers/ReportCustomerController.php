<?php

namespace App\Http\Controllers;

use App\DataTables\ReportCustomerDataTable;
use App\Models\Address;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ReportCustomerController extends Controller
{
    public function index(ReportCustomerDataTable $dataTable)
    {
        abort_if(Gate::denies('modified_customer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $addresses = Address::orderByRaw('CAST(address AS SIGNED), address')->get();
        return $dataTable->render('admin.modified.customer.index',compact('addresses'));
    }

    public function approve(Customer $customer)
    {
        $customer->update(['is_verified' => true]);
        return response()->json([
            'success' => true,
            'message' => trans('messages.crud.approve_record'),
            'alert-type' => trans('quickadmin.alert-type.success'),
            'title' => trans('quickadmin.customers.customer'),
        ], 200);
    }

}
