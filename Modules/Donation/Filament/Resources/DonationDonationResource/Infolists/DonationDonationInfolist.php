<?php

namespace Modules\Donation\Filament\Resources\DonationDonationResource\Infolists;

use Filament\Infolists;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class DonationDonationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail Donasi')
                ->schema([
                    Infolists\Components\TextEntry::make('provider_order_id')->label('Order ID'),
                    Infolists\Components\TextEntry::make('campaign.title')->label('Campaign'),
                    Infolists\Components\TextEntry::make('donor_name')->label('Donor'),
                    Infolists\Components\TextEntry::make('donor_email')->label('Email'),
                    Infolists\Components\TextEntry::make('amount')->money('idr', locale: 'id'),
                    Infolists\Components\TextEntry::make('status')->badge(),
                    Infolists\Components\TextEntry::make('paid_at')->dateTime(),
                ])->columns(2),
            Section::make('Provider')
                ->schema([
                    Infolists\Components\TextEntry::make('provider_transaction_id')->label('Transaction ID'),
                    Infolists\Components\TextEntry::make('provider_payment_type')->label('Payment Type'),
                    Infolists\Components\TextEntry::make('provider_raw_response')
                        ->label('Raw Response')
                        ->formatStateUsing(function ($state) {
                            if (!$state) {
                                return '-';
                            }
                            return '<pre>' . json_encode($state, JSON_PRETTY_PRINT) . '</pre>';
                        })
                        ->html()
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(),
        ]);
    }
}
