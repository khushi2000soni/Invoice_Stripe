<?php

namespace App\Http\Requests\Staff;

use App\Rules\TitleValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class StaffCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        abort_if((Gate::denies('staff_create')), Response::HTTP_FORBIDDEN, '403 Forbidden');
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
            'name' => ['required','string','max:150','unique:users,name', new TitleValidationRule],
            'username' => ['required','string','max:40','unique:users,username'],
            'email' => ['required','email','unique:users,email'],
            'phone' => ['nullable','digits:10','numeric'],
            'role_id'=>['required','numeric'],
            'password'   => ['required', 'string', 'min:4','confirmed'],
            'password_confirmation' => ['required','min:4','same:password'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Please provide a name for the staff member.',
            'name.string' => 'The name should be a valid string.',
            'name.max' => 'The name should not exceed 150 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already in use.',
            'password.confirmed' => 'Password and Confirm Password should match.',
            'role_id.required'=> 'Role is required',
            // Add custom messages for other rules.
        ];
    }
}
