<?php

namespace App\Http\Controllers;

use App\DataTables\AddressDataTable;
use App\Exports\AddressExport;
use App\Models\Address;
use App\Rules\TitleValidationRule;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(AddressDataTable $dataTable)
    {
        abort_if(Gate::denies('address_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $addresses = Address::orderByRaw('CAST(address AS SIGNED), address')->get();
        // return response address
        return $dataTable->render('admin.address.index',compact('addresses'));

    }

    public function printView($address_id = null)
    {
        abort_if(Gate::denies('address_print'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $query = Address::query();
        if ($address_id !== null) {
            $query->where('id', $address_id);
        }
        $addresses = $query->orderByRaw('CAST(address AS SIGNED), address')->get();
        return view('admin.address.print-address-list',compact('addresses'))->render();
    }

    public function export($address_id = null){
        abort_if(Gate::denies('address_export'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return Excel::download(new AddressExport($address_id), 'cities.xlsx');
    }

    public function create()
    {
        $htmlView = view('admin.address.create')->render();
        return response()->json(['success' => true, 'htmlView' => $htmlView]);
    }

    public function store(Request $request)
    {
        $validatedData =$request->validate([
            'address' => ['required','string','unique:address,address', new TitleValidationRule],
        ],[
            'address.regex'=> 'This field should not contain multiple consecutive spaces or consist of only spaces.'
        ]);

        $address=Address::create($validatedData);
        return response()->json([
            'success' => true,
            'message' => trans('messages.crud.add_record'),
            'alert-type' => trans('quickadmin.alert-type.success'),
            'address' => [
                'id' => $address->id,
                'address' => $address->address,
            ],
        ], 200);
    }

    public function edit($id)
    {
        $address = Address::findOrFail($id);
        $htmlView = view('admin.address.edit', compact('address'))->render();
        return response()->json(['success' => true, 'htmlView' => $htmlView]);
    }

    public function update(Request $request, $id)
    {
        $address = Address::find($id);
        $validatedData =$request->validate([
            'address' => ['required','string','unique:address,address,'.$address->id, new TitleValidationRule],
        ],[
            'address.regex'=> 'This field should not contain multiple consecutive spaces or consist of only spaces.'
        ]);

        $address->update($validatedData);
        return response()->json([
            'success' => true,
            'message' => trans('messages.crud.update_record'),
            'alert-type'=> trans('quickadmin.alert-type.success'),
            'redirecturl'=>route('address.index'),
            'title' => trans('quickadmin.address.address')], 200);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('address_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $address = Address::findOrFail($id);
        $address->delete();
        return response()->json(['success' => true,
         'message' => trans('messages.crud.delete_record'),
         'alert-type'=> trans('quickadmin.alert-type.success'),
         'title' => trans('quickadmin.address.address')
        ], 200);
    }
}
