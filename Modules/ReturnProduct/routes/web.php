<?php

use Illuminate\Support\Facades\Route;
use Modules\ReturnProduct\Http\Controllers\ReturnProductController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('returnproducts', ReturnProductController::class)->names('returnproduct');
});
