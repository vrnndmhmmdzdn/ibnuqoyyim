<?php

namespace Modules\DynamicForm;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class DynamicFormServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        
        // Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'dynamic-form');
        
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        
        // Register Filament Resources and Pages
        if (class_exists(Filament::class)) {
            Filament::serving(function () {
                Filament::registerResources([
                    \Modules\DynamicForm\Filament\Resources\FormResource::class,
                    \Modules\DynamicForm\Filament\Resources\FormSubmissionResource::class,
                ]);
            });
        }
    }
}

