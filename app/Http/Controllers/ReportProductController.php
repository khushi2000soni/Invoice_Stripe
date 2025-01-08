<?php

namespace App\Http\Controllers;

use App\DataTables\ReportProductDataTable;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ReportProductController extends Controller
{
    public function index(ReportProductDataTable $dataTable)
    {
        abort_if(Gate::denies('modified_product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $products = Product::orderBy('id','desc')->get();
        $categories = Category::orderBy('id','desc')->get();
        return $dataTable->render('admin.modified.product.index',compact('products','categories'));
    }

    public function approve(Product $product)
    {
        //dd($product);
        $product->update(['is_verified' => true]);
        return response()->json([
            'success' => true,
            'message' => trans('messages.crud.approve_record'),
            'alert-type' => trans('quickadmin.alert-type.success'),
            'title' => trans('quickadmin.product.product'),
        ], 200);
    }
}
