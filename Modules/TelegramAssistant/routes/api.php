<?php

use Illuminate\Support\Facades\Route;
use Modules\TelegramAssistant\Http\Controllers\TelegramWebhookController;

Route::post('/telegram/webhook', TelegramWebhookController::class)
    ->name('telegram.webhook');