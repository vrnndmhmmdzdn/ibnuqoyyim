<?php

namespace Modules\JurnalGuru;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class JurnalGuruServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        
        // Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'jurnal-guru');
        
        // Register Filament Resources and Pages
        if (class_exists(Filament::class)) {
            Filament::serving(function () {
                Filament::registerResources([
                    \Modules\JurnalGuru\Filament\Resources\JurnalGuruResource::class
                ]);
            });
        }
    }
}