<?php

namespace Modules\Donation\Filament\Widgets;

use Modules\Donation\Models\DonationDonation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DonationTotalMonthWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $total = DonationDonation::query()
            ->where('status', 'paid')
            ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        return [
            Stat::make('Total Donasi Bulan Ini', 'Rp ' . number_format($total, 0, ',', '.')),
        ];
    }
}
