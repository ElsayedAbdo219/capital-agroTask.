<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\V1\UserController;
use Modules\User\Http\Controllers\V1\AuthController;
// Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forget-Password', [AuthController::class, 'forgetPassword']);
// Route::post('/verify-account', [AuthController::class, 'verifyAccount']);
Route::post('/verify-Otp', [AuthController::class, 'verifyotp']);
Route::post('/resendOtp', [AuthController::class, 'resendOtp']);
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::prefix('users')->name('users.')->group(function(){
      Route::get('/',[UserController::class,'index']);
      Route::post('/',[UserController::class,'store']);
      Route::get('/{user}',[UserController::class,'show']);
      Route::put('/{user}',[UserController::class,'update']);
      Route::delete('/{user}',[UserController::class,'delete']);
    });
});
