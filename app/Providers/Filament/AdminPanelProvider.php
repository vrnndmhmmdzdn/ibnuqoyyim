<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->discoverResources(in: base_path('Modules/Guru/Filament/Resources'), for: 'Modules\Guru\Filament\Resources')
            ->discoverPages(in: base_path('Modules/Guru/Filament/Pages'), for: 'Modules\Guru\Filament\Pages')
            ->discoverResources(in: base_path('Modules/TahunAjaran/Filament/Resources'), for: 'Modules\TahunAjaran\Filament\Resources')
            ->discoverPages(in: base_path('Modules/TahunAjaran/Filament/Pages'), for: 'Modules\TahunAjaran\Filament\Pages')
            ->discoverResources(in: base_path('Modules/MataPelajaran/Filament/Resources'), for: 'Modules\MataPelajaran\Filament\Resources')
            ->discoverPages(in: base_path('Modules/MataPelajaran/Filament/Pages'), for: 'Modules\MataPelajaran\Filament\Pages')
            ->discoverResources(in: base_path('Modules/Angkatan/Filament/Resources'), for: 'Modules\Angkatan\Filament\Resources')
            ->discoverPages(in: base_path('Modules/Angkatan/Filament/Pages'), for: 'Modules\Angkatan\Filament\Pages')
            ->discoverResources(in: base_path('Modules/Kelas/Filament/Resources'), for: 'Modules\Kelas\Filament\Resources')
            ->discoverPages(in: base_path('Modules/Kelas/Filament/Pages'), for: 'Modules\Kelas\Filament\Pages')

            ->discoverResources(in: base_path('Modules/MediaAsset/Filament/Resources'), for: 'Modules\MediaAsset\Filament\Resources')
            ->discoverPages(in: base_path('Modules/MediaAsset/Filament/Pages'), for: 'Modules\MediaAsset\Filament\Pages')

            ->discoverResources(in: base_path('Modules/KalenderDidik/Filament/Resources'), for: 'Modules\KalenderDidik\Filament\Resources')
            ->discoverPages(in: base_path('Modules/KalenderDidik/Filament/Pages'), for: 'Modules\KalenderDidik\Filament\Pages')
            ->discoverResources(in: base_path('Modules/DynamicForm/Filament/Resources'), for: 'Modules\DynamicForm\Filament\Resources')
            ->discoverPages(in: base_path('Modules/DynamicForm/Filament/Pages'), for: 'Modules\DynamicForm\Filament\Pages')
            ->discoverResources(in: base_path('Modules/Donation/Filament/Resources'), for: 'Modules\Donation\Filament\Resources')
            ->discoverPages(in: base_path('Modules/Donation/Filament/Pages'), for: 'Modules\Donation\Filament\Pages')
            ->discoverResources(in: base_path('Modules/Forum/Filament/Resources'), for: 'Modules\Forum\Filament\Resources')
            ->discoverPages(in: base_path('Modules/Forum/Filament/Pages'), for: 'Modules\Forum\Filament\Pages')
            ->discoverResources(in: base_path('Modules/Siswa/Filament/Resources'), for: 'Modules\Siswa\Filament\Resources')
            ->discoverPages(in: base_path('Modules/Siswa/Filament/Pages'), for: 'Modules\Siswa\Filament\Pages')
            ->discoverResources(in: base_path('Modules/AuditLog/Filament/Resources'), for: 'Modules\AuditLog\Filament\Resources')
            ->discoverResources(in: base_path('Modules/JadwalPelajaran/Filament/Resources'), for: 'Modules\JadwalPelajaran\Filament\Resources')
            ->discoverPages(in: base_path('Modules/JadwalPelajaran/Filament/Pages'), for: 'Modules\JadwalPelajaran\Filament\Pages')
            ->discoverResources(in: base_path('Modules/JurnalGuru/Filament/Resources'), for: 'Modules\JurnalGuru\Filament\Resources')
            ->discoverPages(in: base_path('Modules/JurnalGuru/Filament/Pages'), for: 'Modules\JurnalGuru\Filament\Pages')
            ->discoverResources(in: base_path('Modules/Penilaian/Filament/Resources'), for: 'Modules\Penilaian\Filament\Resources')
            ->discoverPages(in: base_path('Modules/Penilaian/Filament/Pages'), for: 'Modules\Penilaian\Filament\Pages')
            ->discoverResources(in: base_path('Modules/AbsensiStaf/Filament/Resources'), for: 'Modules\AbsensiStaf\Filament\Resources')
            ->discoverPages(in: base_path('Modules/AbsensiStaf/Filament/Pages'), for: 'Modules\AbsensiStaf\Filament\Pages')
            ->discoverResources(in: base_path('Modules/MutabaahTahfidz/Filament/Resources'), for: 'Modules\MutabaahTahfidz\Filament\Resources')
            ->discoverPages(in: base_path('Modules/MutabaahTahfidz/Filament/Pages'), for: 'Modules\MutabaahTahfidz\Filament\Pages')
            ->discoverResources(in: base_path('Modules/KelasPivot/Filament/Resources'), for: 'Modules\KelasPivot\Filament\Resources')
            ->discoverPages(in: base_path('Modules/KelasPivot/Filament/Pages'), for: 'Modules\KelasPivot\Filament\Pages')
            ->discoverResources(in: base_path('Modules/DashboardBuilder/Filament/Resources'), for: 'Modules\DashboardBuilder\Filament\Resources')
            ->discoverPages(in: base_path('Modules/DashboardBuilder/Filament/Pages'), for: 'Modules\DashboardBuilder\Filament\Pages')
            ->discoverResources(in: base_path('Modules/Midtrans/Filament/Resources'), for: 'Modules\Midtrans\Filament\Resources')
            ->discoverPages(in: base_path('Modules/Midtrans/Filament/Pages'), for: 'Modules\Midtrans\Filament\Pages');
            // ->discoverResources(in: base_path('Modules/TelegramAssistant/Filament/Resources'), for: 'Modules\TelegramAssistant\Filament\Resources');
        }
}