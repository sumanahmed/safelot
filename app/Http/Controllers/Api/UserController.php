<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\{ Request, Response, JsonResponse};
use App\Http\Traits\ResponseTrait;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ResponseTrait;

    protected $user;

    public function __construct(User $user)
    {        
        $this->user   = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {  
        $data = $this->user->all();

        return $this->sendResponse($data, Response::HTTP_OK, config("constants.success.data_fetches_success"));
    }

    /**
     * Display the authenticated user's profile.
     *
     * @return JsonResponse
     */
    public function show()
    {   
        $authId = auth()->user()->id;
        $data   = $this->user->find($authId);
        
        return $this->sendResponse($data, Response::HTTP_OK, config("constants.success.data_fetches_success"));
    }

    /**
     * Change the password for the authenticated user.
     *
     * @param Request $request
     * @return array
     */
    public function changePassword(Request $request)
    {           
        $validator = Validator::make($request->all(), [
            'old_password'          => 'required',
            'new_password'          => 'min:8|required_with:confirm_new_password|same:confirm_new_password',
            'confirm_new_password'  => 'min:8'
        ]);

        if ($validator->fails()) {
            return [
                'errors' => $validator->errors(),
                'isFormValid' => false
            ];
        }
    
        $user = auth()->user();

        // Check if the old password matches the current password
        if (!Hash::check($request->old_password, $user->password)) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, "Old password does not match");
        }

        // Update the password
        $user->update(['password' => bcrypt($request->new_password)]);
        
        return $this->sendResponse([], Response::HTTP_OK, "Password changed successfully");
    }
}
