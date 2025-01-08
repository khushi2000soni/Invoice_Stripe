<?php

namespace App\Http\Controllers;

use App\DataTables\ProductDataTable;
use App\Exports\ProductExport;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Models\Category;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index(ProductDataTable $dataTable)
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $products = Product::orderBy('id','desc')->get();
        $categories = Category::orderBy('id','desc')->get();
        return $dataTable->render('admin.product.index',compact('products','categories'));
    }

    public function printView($category_id = null, $product_id = null)
    {
        abort_if(Gate::denies('product_print'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $query = Product::query();
        if ($category_id !== null && $category_id != 'null') {
            $query->where('category_id', $category_id);
        }
        if ($product_id !== null) {
            $query->where('id', $product_id);
        }
        $products = $query->orderBy('id','desc')->get();
        return view('admin.product.print-product-list',compact('products'))->render();
    }

    public function export($category_id = null, $product_id = null){
        abort_if(Gate::denies('product_export'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return Excel::download(new ProductExport($category_id,$product_id), 'Items-List.xlsx');
    }

    public function create()
    {
        $categories = Category::orderBy('id','DESC')->get();
        $htmlView = view('admin.product.create', compact('categories'))->render();
        return response()->json(['success' => true, 'htmlView' => $htmlView]);
    }

    public function store(StoreRequest $request)
    {
        $input = $request->all();
        if ((auth()->user()->hasRole(config('app.roleid.super_admin')))) {
            $input['is_verified']=true;
        }

        $product=Product::create($input);
        return response()->json(['success' => true,
        'message' => trans('messages.crud.add_record'),
        'alert-type'=> trans('quickadmin.alert-type.success'),
        'title' => trans('quickadmin.product.product'),
        'product' => [
            'id' => $product->id,
            'name' => $product->full_name,
            ],
        'selectdata' => [
            'id' => $product->id,
            'name' => $product->full_name,
            'formtype' => 'product',
        ],
        ], 200);
    }

    public function mergeForm($id){
        $product = Product::findOrFail($id);
        $allproducts = Product::orderBy('id','desc')->get();
        $htmlView = view('admin.product.merge-form', compact('product','allproducts'))->render();
        return response()->json(['success' => true, 'htmlView' => $htmlView]);
    }

    public function mergeProduct(Request $request)
    {
        //
       // dd($request->all());
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate([
            'from_product_id' => 'required|exists:products,id',
            'to_product_id' => 'required|exists:products,id',
        ]);

        // Find and update records in order_products table (including trashed records)
        OrderProduct::withTrashed()
            ->where('product_id', $request->from_product_id)
            ->update(['product_id' => $request->to_product_id]);

        // Delete the product from the products table
        Product::find($request->from_product_id)->delete();

        return response()->json(['success' => true,
        'message' => trans('messages.crud.merge_record'),
        'alert-type'=> trans('quickadmin.alert-type.success'),
        'title' => trans('quickadmin.product.product'),
        ], 200);
    }

    public function edit($id)
    {
        $product = Product::with('category')->findOrFail($id);
        $categories = Category::orderBy('id','desc')->get();
        $htmlView = view('admin.product.edit', compact('categories','product'))->render();
        return response()->json(['success' => true, 'htmlView' => $htmlView]);
    }

    public function update(UpdateRequest $request, Product $product)
    {
        $product->update($request->all());
        return response()->json(['success' => true,
        'message' => trans('messages.crud.update_record'),
        'alert-type'=> trans('quickadmin.alert-type.success')], 200);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['success' => true,
         'message' => trans('messages.crud.delete_record'),
         'alert-type'=> trans('quickadmin.alert-type.success'),
         'title' => trans('quickadmin.product.product')
        ], 200);
    }
}
