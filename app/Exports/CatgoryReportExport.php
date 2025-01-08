<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CatgoryReportExport implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Category::getFilteredCategories($this->request);
        $catdata = $query->get();
        $totalAmount = $catdata->sum('amount');
            return $catdata->map(function ($data, $key) use ($totalAmount) {
            return [
                trans('quickadmin.qa_sn') => $key + 1,
                trans('quickadmin.qa_category_name') => $data->name ?? '' ,
                trans('quickadmin.reports.total_product') =>$data->totalsoldproduct ?? '',
                trans('quickadmin.reports.sale_amount') =>$data->amount ?? '',
                trans('quickadmin.reports.sale_percent') => CategoryAmountPercent($data->amount, $totalAmount) ?? '0',
            ];
        });
    }

    public function headings(): array
    {
        return [trans('quickadmin.qa_sn') , trans('quickadmin.qa_category_name') , trans('quickadmin.reports.total_product'), trans('quickadmin.reports.sale_amount'), trans('quickadmin.reports.sale_percent')];
    }
}
