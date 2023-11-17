<?php

namespace App\Services\FormValidation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DeviceInfoFormService implements IFormValidation
{
    protected $id = 0;
    protected $vehicle_id = 0;

    public function __constructor($id, $vehicle_id)
    {
        $this->id = $id;
        $this->vehicle_id = $vehicle_id;
    }
    
    public function rules(): array
    {
        $id = $this->id;
        $vehicle_id = $this->vehicle_id;

        return [
            'name' => [
                'required',
                Rule::unique('device_infos')->where(function ($query) use ($vehicle_id, $id) {
                    return $query->where('vehicle_id', $vehicle_id)->where('id', '!=', $id);
                }),
            ],
            'model'         => 'required',
            'vehicle_id'    => 'required',
        ];
    }

    public function message(): array
    {
        return [            
            'name.required'         => 'Device name is required',
            'name.unique'           => 'Device name already exist',
            'model.required'        => 'Model is required',
            'vehicle_id.required'   => 'Vehicle is required',
        ];
    }

    public function validate(Request $request, $id)
    {
        $this->id  = $id;
        $this->vehicle_id  = $request->vehicle_id;

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