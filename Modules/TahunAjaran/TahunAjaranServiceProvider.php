<?php

namespace Modules\TahunAjaran;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class TahunAjaranServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__.'/resources/views', 'tahun-ajaran');
        
        // Register Filament Resources
        if (class_exists(Filament::class)) {
            Filament::serving(function () {
                Filament::registerResources([
                    \Modules\TahunAjaran\Filament\Resources\TahunAjaranResource::class
                ]);
            });
        }
    }
}