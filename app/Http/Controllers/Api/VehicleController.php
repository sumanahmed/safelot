<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\{ Request, Response, JsonResponse};
use App\Services\FormValidation\IFormValidation;
use App\Http\Traits\ResponseTrait;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    use ResponseTrait;

    protected $vehicle;
    protected $validateForm;

    public function __construct(IFormValidation $validateForm, Vehicle $vehicle)
    {        
        $this->validateForm = $validateForm;
        $this->vehicle = $vehicle;
    }

     /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $data = $this->vehicle->where('user_id', auth()->user()->id)->paginate(10);

        return $this->sendResponse($data, Response::HTTP_OK, config("constants.success.data_fetches_success"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $formValidation = $this->validateForm->validate($request, 0);
        
        if (!$formValidation['isFormValid']) {
            return $this->sendResponse($formValidation['errors'], Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation Error.');
        }

        try {
            $requestAll             = $request->all();
            $requestAll['user_id']  = auth()->user()->id;
            $requestAll['photo']    = $this->uploadBase64toPhoto($request->photo_base64, 'vehicle');

            $data = $this->vehicle->create($requestAll);

            return $this->sendResponse($data, Response::HTTP_CREATED, config("constants.success.save_success"));

        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return $this->sendResponse($data, Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation Error'); 
        }
        
        $data = $this->vehicle->find($request->id);

        if (!$data) {
            return $this->sendResponse([], Response::HTTP_NOT_FOUND, config("constants.failed.data_not_found")); 
        }

        return $this->sendResponse($data, Response::HTTP_OK, config("constants.success.data_fetches_success"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $formValidation = $this->validateForm->validate($request, $request->id);
        
        if (!$formValidation['isFormValid']) {
            return $this->sendResponse($formValidation['errors'], Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation Error.');
        }

        $vehicle = $this->vehicle->find($request->id);

        if (!$vehicle) {
            return $this->sendResponse([], Response::HTTP_NOT_FOUND, config("constants.failed.data_not_found")); 
        }

        try {

            $vehicle->update($request->all());

            $data = $this->vehicle->find($request->id);

            return $this->sendResponse($data, Response::HTTP_CREATED, config("constants.success.update_success"));

        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return $this->sendResponse($data, Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation Error'); 
        }
        
        $data = $this->vehicle->find($request->id);

        if (!$data) {
            return $this->sendResponse([], Response::HTTP_NOT_FOUND, config("constants.failed.data_not_found")); 
        }

        $data->delete();

        return $this->sendResponse([], Response::HTTP_OK, config("constants.success.delete_success"));
    }

}
