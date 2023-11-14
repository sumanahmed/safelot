<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{ AuthController, DealershipController, ForgotPasswordController, UserController, VehicleController};

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
        Route::group(['prefix' => 'users'], function () {
            Route::get('/', [UserController::class, 'index'])->name('user.index');
            Route::get('/show', [UserController::class, 'show'])->name('user.index');
            Route::post('/change-password', [UserController::class, 'changePassword'])->name('user.change_password');
        });

        Route::group(['prefix' => 'dealerships'], function () {
            Route::get('/', [DealershipController::class, 'index'])->name('dealership.index');
            Route::post('/store', [DealershipController::class, 'store'])->name('dealership.store');
            Route::get('/show', [DealershipController::class, 'show'])->name('dealership.show');
            Route::put('/update', [DealershipController::class, 'update'])->name('dealership.update');
            Route::delete('/destroy', [DealershipController::class, 'destroy'])->name('dealership.destroy');
        });

        Route::group(['prefix' => 'vehicles'], function () {
            Route::get('/', [VehicleController::class, 'index'])->name('vehicle.index');
            Route::post('/store', [VehicleController::class, 'store'])->name('vehicle.store');
            Route::get('/show', [VehicleController::class, 'show'])->name('vehicle.show');
            Route::put('/update', [VehicleController::class, 'update'])->name('vehicle.update');
            Route::delete('/destroy', [VehicleController::class, 'destroy'])->name('vehicle.destroy');
        });

        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
    });
});

