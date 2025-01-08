<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    //

    public function AllProducts(){

        $allproducts = Product::with('category')->orderBy('id','desc')->get();

        $responseData = [
            'status'    => true,
            'message'   => trans('messages.success'),
            'productData'  => [],
        ];
        foreach ($allproducts as $product) {
            $responseData['productData'][] = [
                'product_id'           => $product->id ?? '',
                'product_name'     => $product->name ?? '',
                'category_id'      => $product->category_id ?? '',
                'category_name'      => $product->category_id ? $product->category->name : '',
            ];
        }

        return response()->json($responseData, 200);
    }

    public function AllCategories(){
        $allcategories = Category::orderBy('id','desc')->get();

        $responseData = [
            'status'    => true,
            'message'   => trans('messages.success'),
            'categoryData'  => [],
        ];
        foreach ($allcategories as $category) {
            $responseData['categoryData'][] = [
                'category_id'           => $category->id ?? '',
                'category_name'     => $category->name ?? '',
            ];
        }

        return response()->json($responseData, 200);
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(),[
        'name' => ['required','string','max:150','unique:products,name'/*, 'regex:/^[^\s]+(?:\s[^\s]+)?$/'*/],
            'category_id'=>['required','numeric'],
        ]);

        if($validator->fails()){
            $responseData = [
                'status'        => false,
                'validation_errors' => $validator->errors(),
            ];
            return response()->json($responseData, 401);
        }

        try{
            $input = $request->all();
            $product=Product::create($input);
            $responseData = [
                'status'            => true,
                'message'           => trans('messages.success'),
            ];
            return response()->json($responseData, 200);
        }catch (\Exception $e) {
            //dd($e->getMessage().'->'.$e->getLine());
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 401);
        }

    }
}
