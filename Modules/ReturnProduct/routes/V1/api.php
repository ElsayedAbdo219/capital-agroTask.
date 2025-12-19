<?php

use Illuminate\Support\Facades\Route;
use Modules\ReturnProduct\Http\Controllers\V1\ReturnProductController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
   Route::prefix('return-products')->group(function () {
       Route::get('/', [ReturnProductController::class, 'index']);
       Route::post('/', [ReturnProductController::class, 'store']);
       Route::get('/{returnProduct}', [ReturnProductController::class, 'show']);
       Route::put('/{returnProduct}', [ReturnProductController::class, 'update']);
       Route::delete('/{returnProduct}', [ReturnProductController::class, 'delete']);
   });
});
