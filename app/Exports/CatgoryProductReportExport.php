<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CatgoryProductReportExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $category_id = $this->request->category_id ;
        $from_date = $this->request->from_date ?? null;
        $to_date = $this->request->to_date ?? null;
        $category_percent = $this->request->category_percent ?? null;
        $category_name = Category::find($category_id)->name;
        $duration = $from_date && $to_date ? Carbon::parse($from_date)->format('d-m-Y') . ' to ' . Carbon::parse($to_date)->format('d-m-Y') : null;
        $query = Product::getFilteredProducts($this->request);
        $alldata = $query->get();
        $totalAmount = $alldata->sum('amount');

        return view('admin.report.category.export-report-category-product',compact('alldata','category_percent','totalAmount','category_name', 'duration'));
    }

}
