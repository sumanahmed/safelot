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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'=> 'required',
            'last_name' => 'nullable',
            'email'     => 'required',
        ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return $this->sendResponse($data, Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation Error'); 
        }

        $user = $this->user->find($request->id);

        if (!$user) {
            return $this->sendResponse([], Response::HTTP_NOT_FOUND, config("constants.failed.data_not_found")); 
        }

        try {

            $user->update($request->all());

            $data = $this->user->find($request->id);

            return $this->sendResponse($data, Response::HTTP_CREATED, config("constants.success.update_success"));

        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
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

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {   
        $authUserId = auth()->user()->id;        
        $user = $this->user->find($authUserId);

        if (!$user) {
            return $this->sendResponse([], Response::HTTP_NOT_FOUND, config("constants.failed.data_not_found")); 
        }

        try {

            $user->delete();
            auth()->user()->tokens()->delete();

        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage());
        }

        return $this->sendResponse([], Response::HTTP_OK, config("constants.success.delete_success"));
    }
}
