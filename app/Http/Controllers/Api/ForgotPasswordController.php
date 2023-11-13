<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\ResponseTrait;
use App\Services\AuthService;
use App\Models\User;
use DB;

class ForgotPasswordController extends Controller
{
    use ResponseTrait;

    private $user;
    private $authService;

    public function __construct(User $user, AuthService $authService)
    {        
        $this->authService  = $authService;
        $this->user = $user;
    }

    /**
     * email verify exist or not
     * @parameter $request
     * @return \Illuminate\Http\Response
     */
    public function emailVerify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return $this->sendResponse($data, Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation Error'); 
        }

        try {

            return $this->authService->otpGenerateAndSendToEmail($request);
        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
    }

    /**
     * OTP verify
     * @parameter $request
     * @return \Illuminate\Http\Response
     */
    public function otpVerify(Request $request)
    {  
        $validator = Validator::make($request->all(), [
            'otp'   => 'required',
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->fails()) {
            return [
                'errors' => $validator->errors(),
                'isFormValid' => false
            ];
        }

        $otpExist = DB::table('password_resets')->where(['token' => $request->otp, 'email' => $request->email])->first();

        if (!$otpExist) {
            return $this->sendResponse([], Response::HTTP_NOT_FOUND, 'Sorry, your OTP does not match'); 
        }

        try {

            $this->user->where('email', $request->email)->update(['otp_verified' => 1]);
            DB::table('password_resets')->where(['email'=> $request->email])->delete();

            return $this->sendResponse([], Response::HTTP_OK, 'OTP verified successfully'); 
        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
    }

    /**
     * change password
     * @parameter $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'             => 'required|email|exists:users',
            'new_password'      => 'min:6|required_with:confirm_new_password|same:confirm_new_password',
            'confirm_new_password'  => 'min:6'
        ]);

        if ($validator->fails()) {
            return [
                'errors' => $validator->errors(),
                'isFormValid' => false
            ];
        }

        try {

            $this->user->where('email', $request->email)->update(['password' => bcrypt($request->password)]);

            return $this->sendResponse([], Response::HTTP_OK, 'Password changed successfully'); 
        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
    }


}
