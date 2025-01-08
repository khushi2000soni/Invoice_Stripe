<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_if((Gate::denies('invoice_create')), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'customer_id' => 'required|integer',
            'thaila_price' => 'nullable|numeric',
            'is_round_off' => 'required|boolean',
            'round_off' => 'nullable|numeric',
            'sub_total'=> 'required|numeric',
            'grand_total' => 'required|numeric',
            'products' => 'required|array',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|numeric',
            'products.*.price' => 'required|numeric',
            'products.*.total_price' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'customer_id.required' => 'The Customer Name is required.',
        ];
    }
}
