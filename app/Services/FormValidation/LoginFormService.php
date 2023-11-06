<?php

namespace App\Services\FormValidation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginFormService implements IFormValidation
{
    
    public function rules(): array
    {

        return [
            'email'    => 'required',
            'password' => 'required',
        ];
    }

    public function message(): array
    {
        return [
            'email.required'    => 'Email address is required',
            'password.required' => 'Password is required'
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