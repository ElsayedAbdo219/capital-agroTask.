<?php

use Illuminate\Support\Facades\Route;
use Modules\OrderItem\Http\Controllers\OrderItemController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('orderitems', OrderItemController::class)->names('orderitem');
});
