<?php

namespace Modules\Kelas;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class KelasServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register any bindings
    }

    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        
        // Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'kelas');
        
        // Register Filament Resources
        if (class_exists(Filament::class)) {
            Filament::serving(function () {
                Filament::registerResources([
                    \Modules\Kelas\Filament\Resources\KelasResource::class
                ]);
            });
        }
    }
}