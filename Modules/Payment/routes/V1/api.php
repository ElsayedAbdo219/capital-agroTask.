<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\V1\PaymentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
  Route::prefix('payments')->group(function () {
    Route::get('getMethods', [PaymentController::class,'getMethods'])->name('getMethods');
    Route::post('createInvoice', [PaymentController::class, 'createInvoice'])->name('payments.createInvoice');
    Route::post('pay-manually', [PaymentController::class, 'payManually'])->name('payments.pay-manually');
});
});
Route::post('webhook_json', [PaymentController::class, 'createInvoice'])->name('payments.webhook_json');
