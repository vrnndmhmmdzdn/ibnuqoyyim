<?php

namespace Modules\Donation\Livewire;

use Livewire\Component;
use Modules\Donation\Models\DonationDonation;
use Modules\Donation\Services\DonationMidtransService;

class DonateCheckoutPage extends Component
{
    public DonationDonation $donation;
    public ?string $clientKey = null;
    public string $environment = 'sandbox';

    public function mount(string $orderId, DonationMidtransService $midtrans): void
    {
        $this->donation = DonationDonation::query()
            ->with('campaign')
            ->where('provider_order_id', $orderId)
            ->firstOrFail();

        $this->clientKey = $midtrans->getClientKey();
        $this->environment = $midtrans->getEnvironment();
    }

    public function render()
    {
        return view('donation::livewire.donate-checkout-page', [
            'campaign' => $this->donation->campaign,
            'donation' => $this->donation,
            'snapToken' => $this->donation->snap_token,
            'clientKey' => $this->clientKey,
            'environment' => $this->environment,
        ])->layout('donation::layouts.app', ['title' => 'Pembayaran Donasi']);
    }
}
