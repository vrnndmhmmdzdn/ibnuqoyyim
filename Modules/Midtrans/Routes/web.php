<?php

use Illuminate\Support\Facades\Route;
use Modules\Midtrans\Http\Controllers\MidtransController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module.
|
*/

Route::prefix('midtrans')->name('midtrans.')->group(function () {
    Route::get('/finish', [MidtransController::class, 'finish'])->name('finish');
    Route::get('/unfinish', [MidtransController::class, 'unfinish'])->name('unfinish');
    Route::get('/error', [MidtransController::class, 'error'])->name('error');
    Route::post('/check-status/{orderId}', [MidtransController::class, 'checkStatus'])->name('check-status');
    Route::get('/callback-error', [MidtransController::class, 'callbackError'])->name('callback-error');
});
