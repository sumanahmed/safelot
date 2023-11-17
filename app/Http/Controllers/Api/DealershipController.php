<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\{ Request, Response, JsonResponse};
use App\Services\FormValidation\IFormValidation;
use App\Http\Traits\ResponseTrait;
use App\Models\{ Dealership, Vehicle, DeviceInfo };
use Illuminate\Support\Facades\Validator;
use DB;

class DealershipController extends Controller
{
    use ResponseTrait;

    protected $dealership;
    protected $vehicle;
    protected $deviceInfo;
    protected $validateForm;

    public function __construct(IFormValidation $validateForm, Dealership $dealership, Vehicle $vehicle, DeviceInfo $deviceInfo)
    {        
        $this->validateForm = $validateForm;
        $this->dealership   = $dealership;
        $this->vehicle      = $vehicle;
        $this->deviceInfo   = $deviceInfo;
    }

     /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $data = $this->dealership->withCount([
                    'vehicles AS total_vehicle' => function ($q) {
                        $q->select(DB::raw("COUNT(*)"));
                    },
                    'devices AS lock_device' => function ($q) {
                        $q->where('status', 1);
                    },
                    'devices AS unlock_device' => function ($q) {
                        $q->where('status', 2);
                    }
                ])
                ->where('user_id', auth()->user()->id)->paginate(10);

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

            $requestAll = $request->all();
            $requestAll['user_id'] = auth()->user()->id;

            $data = $this->dealership->create($requestAll);

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
        
        $data = $this->dealership->find($request->id);

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

        $dealership = $this->dealership->find($request->id);

        if (!$dealership) {
            return $this->sendResponse([], Response::HTTP_NOT_FOUND, config("constants.failed.data_not_found")); 
        }

        try {

            $dealership->update($request->all());

            $data = $this->dealership->find($request->id);

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
        
        $data = $this->dealership->find($request->id);

        if (!$data) {
            return $this->sendResponse([], Response::HTTP_NOT_FOUND, config("constants.failed.data_not_found")); 
        }

        $data->delete();

        return $this->sendResponse([], Response::HTTP_OK, config("constants.success.delete_success"));
    }

    /**
     * get the vehicle list by dealership_id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function vehicleByDealer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dealership_id' => 'required',
        ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return $this->sendResponse($data, Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation Error'); 
        }

        $dealership = $this->dealership->where(['user_id' => auth()->user()->id, 'id' => $request->dealership_id])->first();

        if (!$dealership) {
            return $this->sendResponse([], Response::HTTP_NOT_FOUND, config("constants.failed.data_not_found")); 
        }
        
        try {
            
            $data = $this->vehicle->select('id','vin','nickname','stock','owner_type')->where('dealership_id', $request->dealership_id)->get();
            
            return $this->sendResponse($data, Response::HTTP_OK, config("constants.success.fetch_success"));

        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
    }

    /**
     * all device lock & unlock by dealership_id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function fleetLockUnlock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dealership_id' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return $this->sendResponse($data, Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation Error'); 
        }

        $dealership = $this->dealership->where(['user_id' => auth()->user()->id, 'id' => $request->dealership_id])->first();

        if (!$dealership) {
            return $this->sendResponse([], Response::HTTP_NOT_FOUND, config("constants.failed.data_not_found")); 
        }
        
        try {
            
            $vehicleIds = $this->vehicle->where('dealership_id', $request->dealership_id)->pluck('id')->toArray();
            $this->deviceInfo->whereIn('vehicle_id', $vehicleIds)->update(['status' => $request->status]);
            
            return $this->sendResponse([], Response::HTTP_OK, config("constants.success.update_success"));

        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
    }

}
