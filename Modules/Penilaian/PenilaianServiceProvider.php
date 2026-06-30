<?php

namespace Modules\Penilaian;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class PenilaianServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__.'/resources/views', 'penilaian');
        
        // Register Filament Resources
        if (class_exists(Filament::class)) {
            Filament::serving(function () {
                // Filament::registerResources([
                //     \Modules\Penilaian\Filament\Resources\PenilaianResource::class
                // ]);
                Filament::registerPages([
                    \Modules\Penilaian\Filament\Pages\InputNilai::class,
                    \Modules\Penilaian\Filament\Pages\KonfigurasiPenilaian::class,
                    \Modules\Penilaian\Filament\Pages\LaporanNilai::class,
                    \Modules\Penilaian\Filament\Pages\RekapNilai::class,
                ]);
            });
        }
    }
}