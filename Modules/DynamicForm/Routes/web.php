<?php

use Illuminate\Support\Facades\Route;
use Modules\DynamicForm\Http\Controllers\PublicFormController;

Route::middleware('web')->group(function () {
    Route::get('/form/{slug}', [PublicFormController::class, 'show'])
        ->name('dynamic-form.public');
    
    Route::post('/form/{slug}/submit', [PublicFormController::class, 'submit'])
        ->name('dynamic-form.submit');
});

