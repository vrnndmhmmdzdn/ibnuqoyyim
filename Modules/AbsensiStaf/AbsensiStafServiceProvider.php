<?php

namespace Modules\AbsensiStaf;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class AbsensiStafServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        
        // Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'absensi-staf');
        
        // Register Filament Resources and Pages
        if (class_exists(Filament::class)) {
            Filament::serving(function () {
                Filament::registerResources([
                    \Modules\AbsensiStaf\Filament\Resources\AbsensiStafResource::class
                ]);
            });
        }
    }
}