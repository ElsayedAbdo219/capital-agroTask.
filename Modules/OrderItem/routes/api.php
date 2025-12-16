<?php

use Illuminate\Support\Facades\Route;
use Modules\OrderItem\Http\Controllers\OrderItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('orderitems', OrderItemController::class)->names('orderitem');
});
