<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function AllCities(){

        $allcities = Address::orderBy('id','desc')->get();

        $responseData = [
            'status'    => true,
            'message'   => 'success',
            'cityData'  => [],
        ];
        foreach ($allcities as $city) {
            $responseData['cityData'][] = [
                'city_id'           => $city->id ?? '',
                'city_name'     => $city->address ?? '',
            ];
        }

        return response()->json($responseData, 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'address' => ['required','string','unique:address,address',],
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
            $product=Address::create($input);
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
