<?php

namespace Modules\Donation\Filament\Resources\DonationDonationResource\Tables;

use Modules\Donation\Models\DonationDonation;
use Modules\Donation\Services\DonationMidtransService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;

class DonationDonationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('provider_order_id')->searchable(),
                TextColumn::make('campaign.title')->label('Campaign')->searchable(),
                TextColumn::make('donor_name')->label('Donor'),
                TextColumn::make('amount')->money('idr', locale: 'id'),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'failed' => 'Failed',
                    'expired' => 'Expired',
                    'refunded' => 'Refunded',
                ]),
                SelectFilter::make('campaign_id')
                    ->label('Campaign')
                    ->relationship('campaign', 'title'),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('refreshStatus')
                    ->label('Refresh Status')
                    ->action(function (DonationDonation $record, DonationMidtransService $midtrans) {
                        $status = $midtrans->getTransactionStatus($record->provider_order_id);
                        $record->update([
                            'provider_transaction_id' => $status['transaction_id'] ?? $record->provider_transaction_id,
                            'provider_payment_type' => $status['payment_type'] ?? $record->provider_payment_type,
                            'provider_raw_response' => $status,
                            'status' => match ($status['transaction_status'] ?? 'pending') {
                                'settlement', 'capture' => 'paid',
                                'pending' => 'pending',
                                'expire' => 'expired',
                                'cancel', 'deny', 'failure' => 'failed',
                                'refund', 'partial_refund' => 'refunded',
                                default => $record->status,
                            },
                        ]);
                    })
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
