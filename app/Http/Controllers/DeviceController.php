<?php

namespace App\Http\Controllers;

use App\DataTables\DeviceDataTable;
use App\Http\Requests\Device\StoreRequest;
use App\Http\Requests\Device\UpdateRequest;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class DeviceController extends Controller
{

    public function index(DeviceDataTable $dataTable)
    {
        abort_if(Gate::denies('device_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('admin.device.index');
    }

    public function create()
    {
        $staffs = User::role('staff')->get();
        $htmlView = view('admin.device.create', compact('staffs'))->render();
        return response()->json(['success' => true, 'htmlView' => $htmlView]);
    }

    public function store(StoreRequest $request)
    {
        $device=Device::create($request->all());
        return response()->json(['success' => true,
        'message' => trans('messages.crud.add_record'),
        'alert-type'=> trans('quickadmin.alert-type.success')], 200);
    }

    public function edit($id)
    {
        $device = Device::with('staff')->findOrFail($id);
        $staffs = User::role('staff')->get();
        $htmlView = view('admin.device.edit', compact('staffs','device'))->render();
        return response()->json(['success' => true, 'htmlView' => $htmlView]);
    }

    public function update(UpdateRequest $request, Device $device)
    {
        $device->update($request->all());
        return response()->json(['success' => true,
        'message' => trans('messages.crud.update_record'),
        'alert-type'=> trans('quickadmin.alert-type.success')], 200);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('device_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $device = Device::findOrFail($id);
        $device->delete();
        return response()->json(['success' => true,
         'message' => trans('messages.crud.delete_record'),
         'alert-type'=> trans('quickadmin.alert-type.success'),
         'title' => trans('quickadmin.device.device')
        ], 200);
    }
}
