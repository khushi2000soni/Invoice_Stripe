<?php

namespace App\DataTables;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ReportCustomerDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
     {
         return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('name',function($customer){
                return $customer->name ?? "";
            })
            ->editColumn('guardian_name',function($customer){
                return $customer->guardian_name ?? "";
            })
            ->editColumn('phone',function($customer){
                $ph1= $customer->phone  ?? "";
                $ph2= $customer->phone2 ? ' / '.$customer->phone2 : "";
                $phone= $ph1.$ph2;
                return $phone;
            })
            ->editColumn('address.address',function($customer){
                $address = $customer->address;
                return $address ? $address->address : '';
            })
            ->editColumn('created_at', function ($customer) {
                return $customer->created_at->format('d-m-Y h:i A');
            })
            ->addColumn('action',function($customer){
                $action='';
                if (Gate::check('modified_customer_approve')) {
                    $approveIcon = view('components.svg-icon', ['icon' => 'approve'])->render();
                    $action .= '<form action="'.route('modified.customers.approve', $customer->id).'" method="POST" class="approve-customers-form m-1">
                <button title="'.trans('quickadmin.qa_approve').'" class="btn btn-icon btn-info approve-customers-btn btn-sm">'.$approveIcon.'</button>
                </form>';
                }
                if (Gate::check('modified_customer_edit')) {
                    $editIcon = view('components.svg-icon', ['icon' => 'edit'])->render();
                    $action .= '<button class="btn btn-icon btn-info edit-customers-btn p-1 mx-1" data-href="'.route('customers.edit', $customer->id).'">'.$editIcon.'</button>';
                    }
                return $action;
            })
            ->orderColumn('address.address', function ($query, $keyword) {
                $query->orderBy('address.address', 'asc');
            })
            ->filterColumn('created_at.address', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(customers.created_at,'%d-%M-%Y') like ?", ["%$keyword%"]); //date_format when searching using date
            })
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Customer $model): QueryBuilder
    {
        $model = $model->where('is_verified', 0)->orderBy('updated_at','desc');
        return $model->newQuery()->with('address');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
        ->setTableId('customers-table')
        ->parameters([
            'responsive' => true,
            'pageLength' => 70,
            'lengthMenu' => [[10, 25, 50, 70, 100, -1], [10, 25, 50, 70, 100, 'All']],
        ])
        ->columns($this->getColumns())
        ->minifiedAjax()
        ->dom('lfrtip')
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
            Column::make('name')->title(trans('quickadmin.customers.fields.name')),
            Column::make('guardian_name')->title(trans('quickadmin.customers.fields.guardian_name')),
            Column::make('phone')->title(trans('quickadmin.customers.fields.ph_num')),
            Column::make('address.address')->title(trans('quickadmin.customers.fields.address')),
            Column::make('created_at')->title(trans('quickadmin.customers.fields.created_at')),
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
        return 'Customer_' . date('YmdHis');
    }
}
