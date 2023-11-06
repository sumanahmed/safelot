<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response, JsonResponse};
use App\Http\Traits\ResponseTrait;
use App\Services\FormValidation\IFormValidation;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ResponseTrait;

    private $validateForm;

    public function __construct(IFormValidation $validateForm)
    {        
        $this->validateForm = $validateForm;
    }

    /**
     * @Check login crediential to allow user access.
     * @parameter $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request) {
        $formValidation = $this->validateForm->validate($request, 0);
        
        if (!$formValidation['isFormValid']) {
            return $this->sendResponse($formValidation['errors'], Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation Error.');
        }
        
        if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            return $this->sendResponse([], Response::HTTP_UNAUTHORIZED, config("constants.failed.login"));            
        }
        
        $user = Auth::user();
        $data['user']   = $user;
        $data['token']  = $user->createToken('MyApp')->plainTextToken; 
        $data['type']   = 'bearer'; 

        return $this->sendResponse($data, Response::HTTP_OK, config("constants.success.data_fetches_success"));
    }


    public function logout()
    {  
        Auth::user()->tokens()->delete();
        return $this->sendResponse([], Response::HTTP_OK, 'Successfully logged out');
    }


    public function refresh()
    {
        $user = Auth::user();
        $data['user']   = $user;
        $data['token']  = $user->createToken('MyApp')->plainTextToken; 
        $data['type']   = 'bearer'; 

        return $this->sendResponse($data, Response::HTTP_OK, config("constants.success.data_fetches_success"));
    }
}
