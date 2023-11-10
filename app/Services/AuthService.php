<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ResponseTrait;
use Carbon\Carbon;
use Mail, DB;
use Illuminate\Support\Facades\Log;

class AuthService {

    use ResponseTrait;

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * register
     */
    public function register($request) 
    {
        try {

            $requestAll = $request->all();
            $requestAll['name'] = ($request->first_name && $request->last_name) ? $request->first_name.' '.$request->last_name : $request->first_name;
            $this->user->create($requestAll);

            return $this->otpGenerateAndSendToEmail($request);    

        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
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
            $data['type']   = 'Bearer'; 
    
            return $this->sendResponse($data, Response::HTTP_OK, 'Login Successful'); 

        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
        
    }

    /**
     * login where account_type = 2 or 3
     */
    public function googleAppleLogin($request)
    {
        try {

            $existUser = $this->user->where(['account_type' => $request->account_type, 'email' => $request->email])->first();

            if ($existUser) {
                return $this->loginById($existUser->id);
            }

            return $this->register($request);

        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
        
    }

    public function loginById($userId)
    {       
        try {

            if(!Auth::loginUsingId($userId)){
                return $this->sendResponse([], Response::HTTP_UNAUTHORIZED, config("constants.failed.login"));            
            }
            
            $user = Auth::user();
            $data['user']   = $user;
            $data['token']  = $user->createToken('MyApp')->plainTextToken; 
            $data['type']   = 'Bearer'; 
    
            return $this->sendResponse($data, Response::HTTP_OK, 'Login Successful'); 

        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
    }

    /**
     * OTP generate & send to email address
     */
    public function otpGenerateAndSendToEmail($request)
    {
        try {

            $otp = mt_rand(1111,9999);

            DB::table('password_resets')->insert([  
                'email' => $request->email,   
                'token' => $otp,   
                'created_at' => Carbon::now()  
            ]);

            Mail::send('email.otp', ['otp' => $otp], function($message) use($request){
                $message->to($request->email);
                $message->subject('Reset Password');

            });  

            return $this->sendResponse([], Response::HTTP_CREATED, 'OTP send to email address successfully'); 
        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
    }
}
