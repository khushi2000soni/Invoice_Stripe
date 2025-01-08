<?php

namespace App\Http\Requests\Device;

use App\Rules\TitleValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_if((Gate::denies('device_create')), Response::HTTP_FORBIDDEN, '403 Forbidden');
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
            'name' => ['required','string','max:150'],
            'staff_id' => ['required','numeric','regex:/^[^\s]+$/','unique:devices,staff_id'],
            'device_id' => ['required','string','regex:/^[^\s]+$/','unique:devices,device_id'],
            'device_ip' => ['required','string','regex:/^[^\s]+$/','unique:devices,device_ip'],
            'pin'=>['required','numeric','regex:/^[^\s]+$/'/*,'unique:devices,pin'*/,'digits:4'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The Name is required.',
            'name.string' => 'The name should be a valid string.',
            'name.max' => 'The name should not exceed 150 characters.',
            'device_id.required'=> 'The Device ID is required.',
            'device_id.unique'=> 'The Device ID has already been taken.',
            'device_ip.required'=> 'The Device IP is required.',
            'device_ip.unique'=> 'The Device IP has already been taken.',
            'pin.required'=> 'The PIN Number is required.',
            'pin.unique'=> 'The PIN Number has already been taken.',
            'staff_id.required'=> 'The Staff Name is required.',
        ];
    }
}
