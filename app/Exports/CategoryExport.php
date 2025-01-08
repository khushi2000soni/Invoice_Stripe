<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoryExport implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $category_id;



    public function __construct($category_id)
    {
        $this->category_id = $category_id;
    }

    public function collection()
    {
        $query = Category::query();

        // If $address_id is not null, filter by city
        if ($this->category_id !== null) {
            $query->where('id', $this->category_id);
        }

        $categories = $query->orderBy('id','desc')->get();

        // return Category::all()->map(function ($category, $key) {
            return $categories->map(function ($category, $key) {
            return [
                'Sn.' => $key + 1,
                'Name' => $category->name,
                'Total Item' => $category->products->count() ?? 0,
            ];
        });
    }

    public function headings(): array
    {
        return ["Sn.","Name" , "Total Item"];
    }

}
