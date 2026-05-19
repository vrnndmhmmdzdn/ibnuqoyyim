<?php

namespace Modules\Donation\Filament\Resources\DonationCampaignResource\Infolists;

use Filament\Infolists;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class DonationCampaignInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ringkasan')
                ->schema([
                    Infolists\Components\TextEntry::make('title'),
                    Infolists\Components\TextEntry::make('slug'),
                    Infolists\Components\TextEntry::make('status')->badge(),
                    Infolists\Components\TextEntry::make('target_amount')->money('idr', locale: 'id'),
                    Infolists\Components\TextEntry::make('deadline_at')->dateTime(),
                ])->columns(2),
            Section::make('Progress')
                ->schema([
                    Infolists\Components\TextEntry::make('collected_amount_computed')
                        ->label('Total Paid')
                        ->money('idr', locale: 'id'),
                    Infolists\Components\TextEntry::make('donors_count')
                        ->label('Total Donor')
                        ->state(function ($record) {
                            return $record->paidDonations()->count();
                        }),
                ])->columns(2),
            Section::make('Donasi Terbaru')
                ->schema([
                    Infolists\Components\RepeatableEntry::make('donations')
                        ->label('')
                        ->state(function ($record) {
                            return $record->donations()
                                ->where('status', 'paid')
                                ->orderByDesc('paid_at')
                                ->limit(5)
                                ->get()
                                ->map(function ($donation) {
                                    return [
                                        'display_name' => $donation->is_anonymous
                                            ? 'Anonim'
                                            : ($donation->donor_name ?: 'Hamba Allah'),
                                        'amount' => $donation->amount,
                                        'paid_at' => $donation->paid_at,
                                    ];
                                })
                                ->all();
                        })
                        ->schema([
                            Infolists\Components\TextEntry::make('display_name')->label('Donor'),
                            Infolists\Components\TextEntry::make('amount')->money('idr', locale: 'id'),
                            Infolists\Components\TextEntry::make('paid_at')->since(),
                        ])->columns(3),
                ]),
        ]);
    }
}
