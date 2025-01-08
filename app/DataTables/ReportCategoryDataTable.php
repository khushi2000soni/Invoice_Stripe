<?php

namespace App\DataTables;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ReportCategoryDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */

    public function dataTable(QueryBuilder $query)
    {
        $totalAmount = $query->sum('amount');
        return datatables()
        ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('name', function ($data) use ($totalAmount){
                $productCount = $data->totalsoldproduct;
                $routeParams = ['category_id' => $data->category_id];
                $routeParams['category_percent'] = CategoryAmountPercent($data->amount ,$totalAmount);
                // Check if filter parameters are present
                // if (request()->has('address_id')) {
                //     $routeParams['address_id'] = request('address_id');
                // }
                if (request()->has('from_date')) {
                    $routeParams['from_date'] = request('from_date');
                }

                if (request()->has('to_date')) {
                    $routeParams['to_date'] = request('to_date');
                }

                if ($productCount > 0) {
                    $name = '<button class="category-product-detail" title="' . $productCount . ' ' . trans('quickadmin.qa_record_found') . '" data-href="' . route('reports.category.products', $routeParams) . '">' . ucwords($data->name) . '</button>';
                } else {
                    $name = ucwords($data->name);
                }

                return $name;
            })
            ->editColumn('totalsoldproduct', function ($data) {
                return $data->totalsoldproduct;
            })
            ->editColumn('amount',function($data){
                return $data->amount;
            })
            // ->editColumn('total_amount', function () use ($totalAmount) {
            //     return $totalAmount;
            // })
            ->editColumn('percent_share', function ($data) use ($totalAmount) {
                return CategoryAmountPercent($data->amount ,$totalAmount);
            })
            ->rawColumns(['name']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Category $model): QueryBuilder
    {
        $query = $model->newQuery();
        $request = request();
        $query = Category::getFilteredCategories($request);
        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('categories-table')
                    ->parameters([
                        'responsive' => true,
                        'pageLength' => 70,
                        'lengthMenu' => [[10, 25, 50, 70, 100, -1], [10, 25, 50, 70, 100, 'All']],
                    ])
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('rtip');
                    // ->orderBy(3,'desc');
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title(trans('quickadmin.qa_sn'))->orderable(false)->searchable(false),
            Column::make('name')->title(trans('quickadmin.qa_category_name'))->orderable(false)->searchable(false),
            Column::make('totalsoldproduct')->title(trans('quickadmin.reports.total_product'))->orderable(false)->searchable(false),
            Column::make('amount')->title(trans('quickadmin.reports.sale_amount'))->addClass('text-center')->orderable(false)->searchable(false),
            // Column::make('total_amount')->title(trans('quickadmin.reports.total_amount'))->addClass('text-center')->orderable(false)->searchable(false),
            Column::make('percent_share')->title(trans('quickadmin.reports.sale_percent'))->addClass('text-center')->orderable(false)->searchable(false),
        ];
    }
    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ReportCategory_' . date('YmdHis');
    }
}
