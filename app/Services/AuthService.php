<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ResponseTrait;
use DB;
use Illuminate\Support\Facades\Log;

class AuthService {

    use ResponseTrait;

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * login where type = 1 email
     */
    public function emailLogin($request)
    {
        try {

            if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                return $this->sendResponse([], Response::HTTP_UNAUTHORIZED, config("constants.failed.login"));            
            }
            
            $user = Auth::user();
            $data['user']   = $user;
            $data['token']  = $user->createToken('MyApp')->plainTextToken; 
            $data['type']   = 'bearer'; 
    
            return $this->sendResponse($data, Response::HTTP_OK, 'Login Successful'); 

        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
        
    }

    /**
     * login where type = 2 google
     */
    public function googleLogin($request)
    {
        try {

            if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                return $this->sendResponse([], Response::HTTP_UNAUTHORIZED, config("constants.failed.login"));            
            }
            
            $user = Auth::user();
            $data['user']   = $user;
            $data['token']  = $user->createToken('MyApp')->plainTextToken; 
            $data['type']   = 'bearer'; 
    
            return $this->sendResponse($data, Response::HTTP_OK, 'Login Successful'); 

        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
        
    }
}
