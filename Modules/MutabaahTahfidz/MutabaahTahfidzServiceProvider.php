<?php

namespace Modules\MutabaahTahfidz;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class MutabaahTahfidzServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        
        // Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'mutabaah-tahfidz');
        
        // Register Filament Resources and Pages
        if (class_exists(Filament::class)) {
            Filament::serving(function () {
                Filament::registerResources([
                    \Modules\MutabaahTahfidz\Filament\Resources\MutabaahTahfidzResource::class
                ]);
            });
        }
    }
}