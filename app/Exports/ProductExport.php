<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $category_id;
    protected $product_id;

    public function __construct($category_id,$product_id)
    {
        $this->category_id = $category_id;
        $this->product_id = $product_id;
    }


    public function collection()
    {
        //return Product::all()->map(function ($product, $key) {

            $query = Product::query();
            if ($this->category_id !== null && $this->category_id != 'null') {
                $query->where('category_id', $this->category_id);
            }
            if ($this->product_id !== null) {
                $query->where('id', $this->product_id);
            }
            $products = $query->orderBy('id','desc')->get();
            return $products->map(function ($product, $key) {
            return [
                trans('quickadmin.qa_sn') => $key + 1,
                trans('quickadmin.product.fields.name') => $product->name,
                trans('quickadmin.product.fields.category_name') => $product->category->name ?? '',
                trans('quickadmin.product.fields.order_count') => $product->order_count,
            ];
        });
    }

    public function headings(): array
    {
        return [trans('quickadmin.qa_sn'), trans('quickadmin.product.fields.name') , trans('quickadmin.product.fields.category_name'), trans('quickadmin.product.fields.order_count')];
    }
}
