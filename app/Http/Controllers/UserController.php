<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, Response};
use App\Http\Traits\ResponseTrait;
use App\Services\FormValidation\IFormValidation;
use App\Models\User;

class UserController extends Controller
{
    use ResponseTrait;

    protected $user;
    private $validateForm;

    public function __construct(IFormValidation $validateForm, User $user)
    {
        $this->user = $user;    
        $this->validateForm = $validateForm;
    }

    /**
     * Display a paginated list of users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = $this->user->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Store a newly created user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $formValidation = $this->validateForm->validate($request, 0);

        if (!$formValidation['isFormValid']) {
            return $this->sendResponse($formValidation['errors'], Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation Error.');
        }

        try {

            $requestAll         = $request->all();
            $requestAll['name'] = ($request->first_name && $request->last_name) ? $request->first_name.' '.$request->last_name : $request->first_name;
            $requestAll['password'] = bcrypt($request->password);

            $user = $this->user->create($requestAll);

            return $this->sendResponse($user, Response::HTTP_CREATED, 'User created successfully'); 
        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
    }

    /**
     * Update the specified user.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $formValidation = $this->validateForm->validate($request, $id);

        if (!$formValidation['isFormValid']) {
            return $this->sendResponse($formValidation['errors'], Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation Error.');
        }

        $user = $this->user->find($id);

        if (!$user) {
            return $this->sendResponse([], Response::HTTP_NOT_FOUND, 'Sorry, data not found'); 
        }

        try {

            $requestAll         = $request->all();
            $requestAll['name'] = ($request->first_name && $request->last_name) ? $request->first_name.' '.$request->last_name : $request->first_name;
            $requestAll['password'] = isset($request->password) ? bcrypt($request->password) : $user->password;

            $user->update($requestAll);
            
            return $this->sendResponse($user, Response::HTTP_CREATED, 'User updated successfully'); 
        } catch (\Exception $ex) {
            return $this->sendResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, $ex->getMessage()); 
        }
    }

    /**
     * Change the status of the specified user.
     *
     * @param  int  $id
     * @return Response
     */
    public function statusChange($id)
    {
        $user = $this->user->find($id);

        if (!$user) {
            return $this->sendResponse([], Response::HTTP_NOT_FOUND, 'Sorry, user not found');
        }

        $user->status = $user->status == 1 ? 2 : 1;
        $user->update();

        return $this->sendResponse($user, Response::HTTP_CREATED, 'Status updated successfully.');
    }
}
