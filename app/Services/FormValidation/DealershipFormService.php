<?php

namespace App\Services\FormValidation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DealershipFormService implements IFormValidation
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
            'name'      => [
                'required',
                Rule::unique('dealerships')->where(function ($query) use ($user_id) {
                    return $query->where('user_id', $user_id)->where('id', '!=', $this->id);
                }),
            ],
            'address_1' => 'required',
            'city_1'    => 'required',
            'state_1'   => 'required',
            'zip_1'     => 'required',
        ];
    }

    public function message(): array
    {
        return [            
            'name.required' => 'Dealership name is required',
            'name.unique'   => 'This Dealership already exist for this dealer',
            'address_1'     => 'Address is required',
            'city_1'        => 'City is required',
            'state_1'       => 'State is required',
            'zip_1'         => 'Zip is required'
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