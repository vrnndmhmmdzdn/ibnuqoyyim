<?php

use Illuminate\Support\Facades\Route;
use Modules\Midtrans\Http\Controllers\MidtransController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your module.
|
*/

Route::prefix('midtrans')->name('midtrans.')->group(function () {
    Route::post('/notification', [MidtransController::class, 'notification'])->name('notification');
});
