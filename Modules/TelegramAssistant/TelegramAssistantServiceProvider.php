<?php

namespace Modules\TelegramAssistant;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TelegramAssistantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/telegram-assistant.php', 'telegram-assistant');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        Route::middleware('api')->group(__DIR__ . '/routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\SetTelegramWebhook::class,
            ]);
        }
    }
}