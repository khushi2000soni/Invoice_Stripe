<?php

namespace App\Http\Requests\Product;
use App\Rules\TitleValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_if((Gate::denies('product_edit')), Response::HTTP_FORBIDDEN, '403 Forbidden');
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
            'name' => ['required','string','max:150','unique:products,name,'.$this->product->id,/* Rule::unique('products')->ignore($this->product->id)->where(function ($query) {
                return $query->where('category_id', $this->category_id);
            }),'regex:/^[^\s]+$/'*/],
            'category_id'=>['required','numeric'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The Product Name is required.',
            'name.string' => 'The Product name should be a valid string.',
            'name.max' => 'The Product name should not exceed 150 characters.',
            'category_id.required'=> 'The Category is required.',
        ];
    }
}
