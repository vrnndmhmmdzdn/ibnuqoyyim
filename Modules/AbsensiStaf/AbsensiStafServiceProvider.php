<?php

namespace Modules\AbsensiStaf;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class AbsensiStafServiceProvider extends ServiceProvider
{
    public function boot(): void
{
    $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
    $this->loadViewsFrom(__DIR__ . '/resources/views', 'absensi-staf');

    if (class_exists(Filament::class)) {
        Filament::serving(function () {
            Filament::registerResources([
                \Modules\AbsensiStaf\Filament\Resources\HariLiburResource::class,
            ]);
            Filament::registerPages([
                \Modules\AbsensiStaf\Filament\Pages\ClockInOut::class,
                \Modules\AbsensiStaf\Filament\Pages\AbsensiDashboard::class,
                \Modules\AbsensiStaf\Filament\Pages\RiwayatAbsensi::class,
                \Modules\AbsensiStaf\Filament\Pages\PengajuanIzin::class,
                \Modules\AbsensiStaf\Filament\Pages\ManajemenIzin::class,
                \Modules\AbsensiStaf\Filament\Pages\ExportAbsensi::class,
            ]);
        });
    }
}
}