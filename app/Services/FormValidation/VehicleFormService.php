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
        $user_id = auth()->user()->id;

        return [
            'vin'           => 'required',
            'owner_type'    => 'required',
            'nickname'      => 'required',
            'stock'         => 'required',
            'photo_base64'  => 'required',
            'dealership_id' => 'required_if:owner_type,1|nullable',
        ];
    }

    public function message(): array
    {
        return [            
            'vin.required'          => 'VIN is required',
            'owner_type.required'   => 'Owner type is required',
            'nickname.required'     => 'Nickname is required',
            'stock.required'        => 'Stock is required',
            'photo_base64.required' => 'Photo is required',
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