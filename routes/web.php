<?php

use App\Http\Controllers\{HomeController, UserController, ProfileController };
use Illuminate\Support\Facades\{ Route, Artisan };

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {

    Route::group(['prefix' => 'forgot-password'], function () {
        Route::post('/email-verify', [ForgotPasswordController::class, 'emailVerify'])->name('forgot_password.email_verify');
        Route::post('/otp-verify', [ForgotPasswordController::class, 'otpVerify'])->name('forgot_password.otp_verify');
        Route::post('/change-password', [ForgotPasswordController::class, 'changePassword'])->name('forgot_password.change_password');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::post('/status-change/{id}', [UserController::class, 'statusChange'])->name('users.status_change');
        Route::post('/destroy/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/clear', function () {
    // Cache::store('redis')->flush();
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    // Artisan::call('route:cache');
    return "All cache is cleared";
});

