<?php

namespace Modules\Donation\Filament\Widgets;

use Modules\Donation\Models\DonationDonation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DonationTotalAllTimeWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $total = DonationDonation::query()
            ->where('status', 'paid')
            ->sum('amount');

        return [
            Stat::make('Total Donasi All-time', 'Rp ' . number_format($total, 0, ',', '.')),
        ];
    }
}
