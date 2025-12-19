<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\V1\ProductController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
      Route::prefix('products')->name('products.')->group(function(){
      Route::get('/',[ProductController::class,'index']);
      Route::post('/',[ProductController::class,'store']);
      Route::get('/{product}',[ProductController::class,'show']);
      Route::put('/{product}',[ProductController::class,'update']);
      Route::delete('/{product}',[ProductController::class,'delete']);
    });

});
