<?php

namespace App\DataTables;

use App\Models\Device;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class DeviceDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query)
    {
        return datatables()
        ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('name',function($device){
                return $device->name ?? "";
            })
            ->editColumn('staff.name',function($device){
                $staff = $device->staff;
                return $staff ? $staff->name : '';
            })
            ->editColumn('device_id',function($device){
                return $device->device_id  ?? "";
            })
            ->editColumn('device_ip',function($device){
                return $device->device_ip  ?? "";
            })
            ->editColumn('pin',function($device){
                return $device->pin ?? "";
            })
            ->editColumn('created_at', function ($device) {
                return $device->created_at->format('d-m-Y h:i A');
            })
            ->addColumn('action',function($device){
                $action='';
                if (Gate::check('device_edit')) {
                $editIcon = view('components.svg-icon', ['icon' => 'edit'])->render();
                $action .= '<button class="btn btn-icon btn-info edit-device-btn p-1 mx-1" data-toggle="modal" data-target="#editModal" data-id="'.encrypt($device->id).'" data-href="'.route('device.edit', $device->id).'">'.$editIcon.'</button>';
                }
                if (Gate::check('device_delete')) {
                $deleteIcon = view('components.svg-icon', ['icon' => 'delete'])->render();
                $action .= '<form action="'.route('device.destroy', $device->id).'" method="POST" class="deleteForm m-1">
                <button title="'.trans('quickadmin.qa_delete').'" class="btn btn-icon btn-danger record_delete_btn btn-sm">'.$deleteIcon.'</button>
                </form>';
                }
                return $action;
            })
            ->filterColumn('created_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(devices.created_at,'%d-%M-%Y') like ?", ["%$keyword%"]); //date_format when searching using date
            })

            // ->filterColumn('staff_name', function ($query, $keyword) {
            //     $query->whereHas('staff', function ($q) use ($keyword) {
            //         $q->where('users.name', 'like', "%$keyword%");
            //     });
            // })
            ->rawColumns(['action']);
    }


    /**
     * Get the query source of dataTable.
     */
    public function query(Device $model): QueryBuilder
    {
        $model->orderBy('created_at','desc');
        return $model->newQuery()->with('staff');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('devices-table')
                    ->parameters([
                        'responsive' => true,
                        'pageLength' => 70,
                        'lengthMenu' => [[10, 25, 50, 70, 100, -1], [10, 25, 50, 70, 100, 'All']],
                    ])
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('rtip')
                    ->orderBy(1)
                    ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title(trans('quickadmin.qa_sn'))->orderable(false)->searchable(false),
            Column::make('name')->title(trans('quickadmin.device.fields.name')),
            Column::make('staff.name')->title(trans('quickadmin.device.fields.staff_name')),
            Column::make('device_id')->title(trans('quickadmin.device.fields.device_id')),
            Column::make('device_ip')->title(trans('quickadmin.device.fields.device_ip')),
            Column::make('pin')->title(trans('quickadmin.device.fields.pin')),
            Column::make('created_at')->title(trans('quickadmin.device.fields.created_at')),
            Column::computed('action')
            ->exportable(false)
            ->printable(false)
            ->width(60)
            ->addClass('text-center')->title(trans('quickadmin.qa_action')),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Device_' . date('YmdHis');
    }
}
