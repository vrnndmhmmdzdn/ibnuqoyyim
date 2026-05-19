<?php

namespace Modules\Donation\Filament\Widgets;

use Modules\Donation\Models\DonationCampaign;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class DonationTopCampaignsWidget extends BaseWidget
{
    protected static ?string $heading = 'Top Campaigns';

    protected function getTableQuery(): Builder|Relation|null
    {
        return DonationCampaign::query()
            ->withSum(['donations as paid_total' => function ($query) {
                $query->where('status', 'paid');
            }], 'amount')
            ->orderByDesc('paid_total')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')->label('Campaign'),
            Tables\Columns\TextColumn::make('paid_total')->label('Total')->money('idr', locale: 'id'),
        ];
    }
}
