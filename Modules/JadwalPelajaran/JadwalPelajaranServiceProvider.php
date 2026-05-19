<?php

namespace Modules\JadwalPelajaran;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class JadwalPelajaranServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        
        // Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'jadwal-pelajaran');
        
        // Register Filament Resources and Pages
        if (class_exists(Filament::class)) {
            Filament::serving(function () {
                Filament::registerResources([
                    \Modules\JadwalPelajaran\Filament\Resources\JadwalPelajaranResource::class
                ]);
            });
        }
    }
}