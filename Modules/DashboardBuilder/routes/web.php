<?php

use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;
use Modules\DashboardBuilder\Livewire\PreviewDashboardPage;
use Modules\DashboardBuilder\Livewire\PublishedDashboardPage;

Route::middleware('web')->group(function (): void {
    Route::get('/admin/report/{dashboard}/preview', PreviewDashboardPage::class)
        ->middleware(Authenticate::class)
        ->name('dashboard-builder.preview');

    Route::get('/admin/report/{uuid}/access', function (string $uuid) {
        return redirect()->route('dashboard-builder.published', ['uuid' => $uuid]);
    })
        ->middleware(Authenticate::class)
        ->name('dashboard-builder.published.access');

    Route::get('/report/{uuid}', PublishedDashboardPage::class)
        ->name('dashboard-builder.published');
});
