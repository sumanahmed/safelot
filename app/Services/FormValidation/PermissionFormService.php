<?php

namespace App\Services\FormValidation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionFormService implements IFormValidation
{
    protected $id = 0;

    public function __constructor($id)
    {
        $this->id = $id;
    }
    
    public function rules(): array
    {
        return [
            'name' => 'required|max:255|unique:permissions,name,'. $this->id .',id,deleted_at,NULL',
        ];
    }

    public function message(): array
    {
        return [            
            'name.required'  => 'Name is required',
            'name.unique'    => 'Name already exist'
        ];
    }

    public function validate(Request $request, $id)
    {
        $this->id  = $id;

        $validator = Validator::make($request->all(), $this->rules(), $this->message());

        if ($validator->fails()) {
            return [
                'errors'        => $validator->errors(),
                'isFormValid'   => false
            ];
        }
        
        return [
            'isFormValid' => true
        ];
    }
}