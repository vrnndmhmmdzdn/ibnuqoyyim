<?php

namespace Modules\Donation;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Donation\Livewire\CampaignIndexPage;
use Modules\Donation\Livewire\CampaignShowPage;
use Modules\Donation\Livewire\DonateCheckoutPage;
use Modules\Donation\Livewire\DonateFormPage;
use Modules\Donation\Livewire\DonationStatusPage;

class DonationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'donation');

        if (class_exists(Livewire::class)) {
            Livewire::component('donation.campaign-index-page', CampaignIndexPage::class);
            Livewire::component('donation.campaign-show-page', CampaignShowPage::class);
            Livewire::component('donation.donate-form-page', DonateFormPage::class);
            Livewire::component('donation.donate-checkout-page', DonateCheckoutPage::class);
            Livewire::component('donation.donation-status-page', DonationStatusPage::class);

            // Backward-compatible aliases for already-rendered snapshots.
            Livewire::component('modules.donation.livewire.campaign-index-page', CampaignIndexPage::class);
            Livewire::component('modules.donation.livewire.campaign-show-page', CampaignShowPage::class);
            Livewire::component('modules.donation.livewire.donate-form-page', DonateFormPage::class);
            Livewire::component('modules.donation.livewire.donate-checkout-page', DonateCheckoutPage::class);
            Livewire::component('modules.donation.livewire.donation-status-page', DonationStatusPage::class);
        }

        $this->registerRoutes();

        if (class_exists(Filament::class)) {
            Filament::serving(function () {
                Filament::registerWidgets([
                    \Modules\Donation\Filament\Widgets\DonationTotalMonthWidget::class,
                    \Modules\Donation\Filament\Widgets\DonationTotalAllTimeWidget::class,
                    \Modules\Donation\Filament\Widgets\DonationTopCampaignsWidget::class,
                ]);
            });
        }
    }

    protected function registerRoutes(): void
    {
        if (file_exists(__DIR__ . '/Routes/web.php')) {
            Route::middleware('web')
                ->group(__DIR__ . '/Routes/web.php');
        }
    }
}
