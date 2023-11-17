<?php

namespace App\Services\FormValidation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VehicleFormService implements IFormValidation
{
    protected $id = 0;

    public function __constructor($id)
    {
        $this->id = $id;
    }
    
    public function rules(): array
    {
        return [
            'vin'           => 'required|unique:vehicles,vin,'. $this->id .',id',
            'owner_type'    => 'required',
            'nickname'      => 'required',
            'stock'         => 'required',
            'dealership_id' => 'required_if:owner_type,1|nullable',
        ];
    }

    public function message(): array
    {
        return [            
            'vin.required'          => 'VIN is required',
            'vin.unique'            => 'VIN is already exist',
            'owner_type.required'   => 'Owner type is required',
            'nickname.required'     => 'Nickname is required',
            'stock.required'        => 'Stock is required',
            'dealership_id.required_if'  => 'Dealership Id is required',
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