<?php

use Illuminate\Support\Facades\Route;
use Modules\Donation\Livewire\CampaignIndexPage;
use Modules\Donation\Livewire\CampaignShowPage;
use Modules\Donation\Livewire\DonateCheckoutPage;
use Modules\Donation\Livewire\DonateFormPage;
use Modules\Donation\Livewire\DonationStatusPage;

Route::get('/donation', CampaignIndexPage::class)->name('donation.campaigns.index');
Route::get('/donation/checkout/{orderId}', DonateCheckoutPage::class)->name('donation.checkout');
Route::get('/donation/status', function (\Illuminate\Http\Request $request) {
    $orderId = $request->get('order_id') ?? $request->get('orderId');
    abort_unless($orderId, 404);

    return redirect()->route('donation.status', $orderId);
})->name('donation.status.redirect');
Route::get('/donation/status/{orderId}', DonationStatusPage::class)->name('donation.status');
Route::get('/donation/{campaign:slug}', CampaignShowPage::class)->name('donation.campaigns.show');
Route::get('/donation/{campaign:slug}/donate', DonateFormPage::class)->name('donation.donate.create');
