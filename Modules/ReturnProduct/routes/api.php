<?php

use Illuminate\Support\Facades\Route;
use Modules\ReturnProduct\Http\Controllers\ReturnProductController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('returnproducts', ReturnProductController::class)->names('returnproduct');
});
