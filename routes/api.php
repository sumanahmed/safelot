<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{ AuthController, UserController };

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['baseToken']], function () {

    Route::post('login', [AuthController::class, 'login'])->name('auth.login');

    Route::group(['middleware' => ['auth:sanctum']], function() {
        
        // user routes
        Route::group(['prefix' => 'user'], function () {
            Route::get('/', [UserController::class, 'index'])->name('user.index');
        });


        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
    });
});

