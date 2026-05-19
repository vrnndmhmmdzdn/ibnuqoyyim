<?php

namespace Modules\Donation\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Modules\Donation\Models\DonationCampaign;
use Modules\Donation\Models\DonationDonation;
use Modules\Donation\Models\DonationEvent;
use Modules\Donation\Services\DonationMidtransService;

class DonateFormPage extends Component
{
    public DonationCampaign $campaign;

    public string $amount = '';
    public string $donor_name = '';
    public string $donor_email = '';
    public string $message = '';
    public bool $is_anonymous = false;

    public function mount(DonationCampaign $campaign): void
    {
        abort_unless($campaign->status === 'active', 404);
        $this->campaign = $campaign;
    }

    public function setQuickAmount(int $amount): void
    {
        $this->amount = (string) $amount;
    }

    public function submit(DonationMidtransService $midtrans)
    {
        $validated = $this->validate([
            'amount' => ['required', 'integer', 'min:1000'],
            'donor_name' => ['nullable', 'string', 'max:100'],
            'donor_email' => ['nullable', 'email', 'max:150'],
            'message' => ['nullable', 'string', 'max:200'],
            'is_anonymous' => ['boolean'],
        ]);

        $donation = DB::transaction(function () use ($validated) {
            $orderId = 'DON-' . $this->campaign->id . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

            $donation = DonationDonation::create([
                'campaign_id' => $this->campaign->id,
                'donor_name' => $validated['donor_name'] ?: null,
                'donor_email' => $validated['donor_email'] ?: null,
                'amount' => (int) $validated['amount'],
                'message' => $validated['message'] ?: null,
                'is_anonymous' => (bool) ($validated['is_anonymous'] ?? false),
                'status' => 'pending',
                'provider_order_id' => $orderId,
            ]);

            DonationEvent::create([
                'donation_id' => $donation->id,
                'event_type' => 'created',
                'payload' => [
                    'amount' => $donation->amount,
                ],
            ]);

            return $donation;
        });

        $snap = $midtrans->createSnap($donation);

        $donation->update([
            'snap_token' => $snap['snap_token'],
            'snap_redirect_url' => $snap['snap_url'],
        ]);

        return redirect()->route('donation.checkout', $donation->provider_order_id);
    }

    public function render()
    {
        return view('donation::livewire.donate-form-page', [
            'campaign' => $this->campaign,
        ])->layout('donation::layouts.app', ['title' => 'Donasi ' . $this->campaign->title]);
    }
}
