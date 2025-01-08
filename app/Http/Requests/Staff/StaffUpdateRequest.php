<?php

namespace App\Http\Requests\Staff;
use App\Rules\TitleValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class StaffUpdateRequest extends FormRequest
{


    public function authorize(): bool
    {
        abort_if((Gate::denies('staff_edit')), Response::HTTP_FORBIDDEN, '403 Forbidden');
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
            'name' => ['required','string','max:150','unique:users,name,'.$this->staff->id, new TitleValidationRule],
            'username' => ['required','string','max:40','unique:users,username,'.$this->staff->id],
            'email' => ['required','email','unique:users,email,'.$this->staff->id],
            'phone' => ['nullable','digits:10','numeric'],
            'role_id'=>['required','numeric'],
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
        ];
    }
}
