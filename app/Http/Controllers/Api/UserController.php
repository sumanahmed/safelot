<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\{ Request, Response, JsonResponse};
use App\Http\Traits\ResponseTrait;
use App\Models\User;

class UserController extends Controller
{
    use ResponseTrait;

    private $user;

    public function __construct(User $user)
    {        
        $this->user = $user;
    }

    public function index()
    {  
        $data = $this->user->all();

        return $this->sendResponse($data, Response::HTTP_OK, config("constants.success.data_fetches_success"));
    }
}
