<?php

namespace App\Services\FormValidation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserFormService implements IFormValidation
{
    protected $id = 0;

    public function __constructor($id)
    {
        $this->id  = $id;
    }
    
    public function rules(): array
    {
        return [
            'first_name'    => 'required',
            // 'last_name'     => 'required',
            'email'         => 'required|max:255|unique:users,email,'. $this->id .',id',
            'password'      => 'required_if:'. $this->id .',0|nullable|min:8',
        ];
    }

    public function message(): array
    {
        return [
            'first_name.required'   => 'Fist name is required',
            // 'last_name.required'    => 'Last name is required',
            'email.required'        => 'Email address is required',
            'email.unique'          => 'This email address is already taken',
            'password.required_if'  => 'Password is required',
            'password.min:8'        => 'Password length minimum 8 character'
        ];
    }

    public function validate(Request $request, $id)
    {
        $this->id  = $id;
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