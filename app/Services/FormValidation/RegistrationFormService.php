<?php

namespace App\Services\FormValidation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegistrationFormService implements IFormValidation
{
    
    public function rules(): array
    {
        return [
            'first_name'    => 'required',
            'email'         => 'required',
            'password'      => 'min:6|required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'min:6'
        ];
    }

    public function message(): array
    {
        return [
            'first_name.required'   => 'First name is required',
            'email.required'        => 'Email address is required',
            'password.required'     => 'Password is required'
        ];
    }

    public function validate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), $this->rules(), $this->message());

        if ($validator->fails()) {
            return [
                'errors' => $validator->errors(),
                'isFormValid' => false
            ];
        }
        
        return [
            'isFormValid' => true
        ];
    }
}