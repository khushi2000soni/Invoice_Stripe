<?php

namespace App\DataTables;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProductDataTable extends DataTable
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
            ->editColumn('name',function($product){
                return $product->name ?? "";
            })
            ->editColumn('category.name',function($product){
                $category = $product->category;
                return $category ? $category->name : '';
            })
            ->addColumn('order_count', function ($product) {
                return  $product->order_count;
            })
            ->editColumn('created_at', function ($product) {
                return $product->created_at->format('d-m-Y h:i A');
            })
            ->addColumn('action',function($product){
                $action='';
                if (Gate::check('product_edit')) {
                $editIcon = view('components.svg-icon', ['icon' => 'edit'])->render();
                $action .= '<button class="btn btn-icon btn-info edit-products-btn p-1 mx-1" data-href="'.route('products.edit', $product->id).'">'.$editIcon.'</button>';
                }
                if (Gate::check('product_merge')) {
                    $mergeIcon = view('components.svg-icon', ['icon' => 'merge'])->render();
                    $action .= '<button class="btn btn-icon btn-info merge-button p-1 mx-1" data-href="'.route('products.showMerge', $product->id).'">'.$mergeIcon.'</button>';
                }
                if (Gate::check('product_delete')) {
                    if($product->order_count == 0){
                    $deleteIcon = view('components.svg-icon', ['icon' => 'delete'])->render();
                    $action .= '<form action="'.route('products.destroy', $product->id).'" method="POST" class="deleteForm m-1">
                    <button title="'.trans('quickadmin.qa_delete').'" class="btn btn-icon btn-danger record_delete_btn btn-sm">'.$deleteIcon.'</button>
                    </form>';
                    }
                }
                return $action;
            })
            ->filterColumn('created_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(products.created_at,'%d-%M-%Y') like ?", ["%$keyword%"]); //date_format when searching using date
            })
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Product $model): QueryBuilder
    {
        if(isset(request()->category_id) && request()->category_id){
            $model = $model->where('category_id', request()->category_id);
        }
        if(isset(request()->product_id) && request()->product_id){
            $model = $model->where('id', request()->product_id);
        }
        $model = $model->where('is_verified', 1);
        return $model->newQuery()->with('category');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
        ->setTableId('products-table')
        ->parameters([
            'responsive' => true,
            'pageLength' => 70,
            'lengthMenu' => [[10, 25, 50, 70, 100, -1], [10, 25, 50, 70, 100, 'All']],
        ])
        ->columns($this->getColumns())
        ->minifiedAjax()
        ->dom('lfrtip')
        ->orderBy(1);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title(trans('quickadmin.qa_sn'))->orderable(false)->searchable(false),
            Column::make('name')->title(trans('quickadmin.product.fields.name')),
            Column::make('category.name')->title(trans('quickadmin.product.fields.category_name')),
            Column::make('order_count')->title(trans('quickadmin.product.fields.order_count')),
            Column::make('created_at')->title(trans('quickadmin.product.fields.created_at')),
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
        return 'Product_' . date('YmdHis');
    }
}
