<?php

namespace Modules\Donation\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Donation\Models\DonationCampaign;

class CampaignIndexPage extends Component
{
    use WithPagination;

    public function render()
    {
        $campaigns = DonationCampaign::query()
            ->where('status', 'active')
            ->withSum(['donations as paid_total' => function ($query) {
                $query->where('status', 'paid');
            }], 'amount')
            ->orderByDesc('created_at')
            ->paginate(9);

        return view('donation::campaigns.index', compact('campaigns'))
            ->layout('donation::layouts.app', ['title' => 'Donasi']);
    }
}
