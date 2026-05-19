<?php

namespace Modules\Donation\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Modules\Donation\Models\DonationDonation;
use Modules\Donation\Models\DonationEvent;
use Modules\Donation\Services\DonationMidtransService;

class DonationStatusPage extends Component
{
    public DonationDonation $donation;

    public function mount(string $orderId, DonationMidtransService $midtrans): void
    {
        $this->donation = DonationDonation::query()
            ->with('campaign')
            ->where('provider_order_id', $orderId)
            ->firstOrFail();

        if (in_array($this->donation->status, ['draft', 'pending'], true)) {
            $this->syncStatusFromMidtrans($midtrans);
            $this->donation->refresh()->load('campaign');
        }
    }

    protected function syncStatusFromMidtrans(DonationMidtransService $midtrans): void
    {
        try {
            $rawStatus = $midtrans->getTransactionStatus($this->donation->provider_order_id);
            $status = $rawStatus['transaction_status'] ?? null;
            $updates = $midtrans->buildDonationUpdateData($this->donation, $rawStatus);
            $mappedStatus = $updates['status'];

            $hasStatusChange = $this->donation->status !== $mappedStatus;
            $this->donation->update($updates);

            if ($hasStatusChange) {
                DonationEvent::create([
                    'donation_id' => $this->donation->id,
                    'event_type' => 'status_sync',
                    'payload' => [
                        'status' => $status,
                        'mapped' => $mappedStatus,
                        'source' => 'status_page',
                    ],
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to sync donation status from Midtrans', [
                'order_id' => $this->donation->provider_order_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('donation::status', [
            'donation' => $this->donation,
        ])->layout('donation::layouts.app', ['title' => 'Status Donasi']);
    }
}
