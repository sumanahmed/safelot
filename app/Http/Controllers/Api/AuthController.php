<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response, JsonResponse};
use App\Http\Traits\ResponseTrait;
use App\Models\User;
use App\Services\AuthService;
use App\Services\FormValidation\IFormValidation;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ResponseTrait;

    private $validateForm;
    private $authService;
    private $user;

    public function __construct(IFormValidation $validateForm, User $user, AuthService $authService)
    {        
        $this->validateForm = $validateForm;
        $this->authService  = $authService;
        $this->user         = $user;
    }

    /**
     * @Check login crediential to allow user access.
     * @parameter $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request) 
    {
        $formValidation = $this->validateForm->validate($request, 0);
        
        if (!$formValidation['isFormValid']) {
            return $this->sendResponse($formValidation['errors'], Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation Error.');
        }

        $requestAll = $request->all();
        $requestAll['name'] = $request->first_name.' '.$request->last_name;
        $user = $this->user->create($requestAll);

        $data['user']   = $user;
        $data['token']  = $user->createToken('MyApp')->plainTextToken; 
        $data['type']   = 'bearer'; 

        return $this->sendResponse($data, Response::HTTP_CREATED, 'Registration Successful');
    }

    /**
     * @Check login crediential to allow user access.
     * @parameter $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request) 
    {
        $formValidation = $this->validateForm->validate($request, 0);
        
        if (!$formValidation['isFormValid']) {
            return $this->sendResponse($formValidation['errors'], Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation Error.');
        }

        try {

            if ($request->account_type == 1) {
                return $this->authService->emailLogin($request);
            } else if ($request->account_type == 2) {
                return $this->authService->googleLogin($request->all());
            }

        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
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
