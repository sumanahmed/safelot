<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, Response};
use App\Http\Traits\ResponseTrait;
use App\Models\User;

class UserController extends Controller
{
    use ResponseTrait;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;    
    }

    /**
     * show all users
     */
    public function index()
    {
        $users = $this->user->paginate(2);

        return view('users.index', compact('users'));
    }

    /**
     * status change
     * @param int $id
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
