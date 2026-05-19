<?php

namespace Modules\KalenderDidik;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class KalenderDidikServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        
        // Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'kalender-didik');
        
        // Register Filament Resources and Pages
        if (class_exists(Filament::class)) {
            Filament::serving(function () {
                Filament::registerResources([
                    \Modules\KalenderDidik\Filament\Resources\KaldikResource::class
                ]);
            });
        }
    }
}