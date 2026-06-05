<?php

namespace Modules\KelasPivot;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class KelasPivotServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__.'/resources/views', 'kelas-pivot');
        
        // Register Filament Resources
        if (class_exists(Filament::class)) {
            Filament::serving(function () {
                Filament::registerResources([
                    \Modules\KelasPivot\Filament\Resources\KelasPivotResource::class
                ]);
                Filament::registerPages([
                    \Modules\KelasPivot\Filament\Pages\KelasPivot::class
                ]);
            });
        }
    }
}