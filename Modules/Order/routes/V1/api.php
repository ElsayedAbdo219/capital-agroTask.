<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\V1\OrderController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
      Route::prefix('orders')->name('orders.')->group(function(){
      Route::get('/',[OrderController::class,'index']);
      Route::post('/',[OrderController::class,'store']);
      Route::get('/{order}',[OrderController::class,'show']);
      Route::put('/{order}',[OrderController::class,'update']);
      Route::delete('/{order}',[OrderController::class,'delete']);
    });
});
