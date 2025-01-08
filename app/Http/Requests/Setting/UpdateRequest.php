<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        abort_if((Gate::denies('setting_edit')), Response::HTTP_FORBIDDEN, '403 Forbidden');
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
            'site_logo' => 'image|mimes:jpeg,png,jpg,PNG,JPG|max:2048',
            'favicon' => 'image|mimes:jpeg,png,jpg,PNG,JPG|max:2048',
            'phone_num' => 'nullable|numeric',
            'thaila_price' => 'nullable|numeric',
            'invoice_pdf_top_title' => 'nullable|string|max:120',
        ];
    }


    public function messages()
    {
        return [
            'site_logo' => 'The site logo must be an image.',
            'site_logo.mimes' => 'The site logo must be jpeg,png,jpg,PNG,JPG.',
            'state.site_logo.max'   => 'The site logo maximum size is 2048 KB.',
        ];
    }
}
