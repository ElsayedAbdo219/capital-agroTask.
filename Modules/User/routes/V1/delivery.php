<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\V1\UserController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Route::prefix('users')->name('users.')->group(function(){
    //   Route::get('/',[UserController::class,'index']);
    //   Route::post('/',[UserController::class,'store']);
    //   Route::get('/{user}',[UserController::class,'show']);
    //   Route::put('/{user}',[UserController::class,'update']);
    //   Route::delete('/',[UserController::class,'destroy']);
    // });
});
