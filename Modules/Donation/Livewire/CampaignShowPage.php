<?php

namespace Modules\Donation\Livewire;

use Livewire\Component;
use Modules\Donation\Models\DonationCampaign;

class CampaignShowPage extends Component
{
    public DonationCampaign $campaign;

    public function mount(DonationCampaign $campaign): void
    {
        abort_unless($campaign->status === 'active', 404);
        $this->campaign = $campaign;
    }

    public function render()
    {
        $donations = $this->campaign->donations()
            ->where('status', 'paid')
            ->orderByDesc('paid_at')
            ->limit(10)
            ->get();

        $stats = [
            'collected' => (int) $this->campaign->paidDonations()->sum('amount'),
            'donors' => $this->campaign->paidDonations()->count(),
        ];

        return view('donation::campaigns.show', [
            'campaign' => $this->campaign,
            'donations' => $donations,
            'stats' => $stats,
        ])->layout('donation::layouts.app', ['title' => $this->campaign->title]);
    }
}
