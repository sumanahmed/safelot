<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{ AuthController, ForgotPasswordController, UserController };

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

    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('login-by-id', [AuthController::class, 'loginById'])->name('auth.loginById');

    Route::group(['prefix' => 'forgot-password'], function () {
        Route::post('/email-verify', [ForgotPasswordController::class, 'emailVerify'])->name('forgot_password.email_verify');
        Route::post('/otp-verify', [ForgotPasswordController::class, 'otpVerify'])->name('forgot_password.otp_verify');
        Route::post('/change-password', [ForgotPasswordController::class, 'changePassword'])->name('forgot_password.change_password');
    });

    Route::group(['middleware' => ['auth:sanctum']], function() {
        
        // user routes
        Route::group(['prefix' => 'user'], function () {
            Route::get('/', [UserController::class, 'index'])->name('user.index');
        });


        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
    });
});

