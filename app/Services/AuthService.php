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
    public function afterRegisterOtpSend($request) 
    {   
        try {

            if ($request->account_type == 1) {
                return $this->otpGenerateAndSendToEmail($request); 
            }   

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

            // start of check account verified by OTP
                if ($user->otp_verified == 2) {
                    return $this->sendResponse($user, Response::HTTP_UNPROCESSABLE_ENTITY, 'Your account not verifyed by OTP.'); 
                }
            // end of check account verified by OTP

            // start of check account active or not
                if ($user->status == 2) {
                    return $this->sendResponse($user, Response::HTTP_UNPROCESSABLE_ENTITY, 'Your account not active yet.'); 
                }
            // end of check account active or not

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

            $requestAll = $request->all();
            $requestAll['name'] = ($request->first_name && $request->last_name) ? $request->first_name.' '.$request->last_name : $request->first_name;
            $requestAll['otp_verified'] = $request->account_type != 1 ? 1 : 2;
            $requestAll['status']       = 1; //active
            
            $user = $this->user->create($requestAll);

            $data['user']   = $user;
            $data['token']  = $user->createToken('MyApp')->plainTextToken; 
            $data['type']   = 'Bearer'; 
    
            return $this->sendResponse($data, Response::HTTP_OK, 'Login Successful'); 

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
                $message->subject('OTP Confirmation');

            });  

            return $this->sendResponse([], Response::HTTP_CREATED, 'OTP send to email address successfully'); 
        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
    }
}
