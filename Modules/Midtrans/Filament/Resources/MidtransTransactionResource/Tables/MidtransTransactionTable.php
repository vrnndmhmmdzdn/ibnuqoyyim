<?php

namespace Modules\Midtrans\Filament\Resources\MidtransTransactionResource\Tables;

use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class MidtransTransactionTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->searchable()
                    ->toggleable()
                    ->copyable(),

                TextColumn::make('gross_amount')
                    ->label('Amount')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('payment_type')
                    ->label('Payment Type')
                    ->searchable()
                    ->toggleable(),

                BadgeColumn::make('transaction_status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => fn($state) => in_array($state, ['capture', 'settlement']),
                        'danger' => fn($state) => in_array($state, ['deny', 'cancel', 'expire']),
                    ])
                    ->formatStateUsing(fn(string $state): string => Str::upper($state)),

                BadgeColumn::make('updated_via')
                    ->label('Updated Via')
                    ->colors([
                        'success' => 'webhook',
                        'info' => 'callback',
                        'warning' => 'ajax',
                        'gray' => fn($state) => in_array($state, ['manual', 'legacy']),
                    ])
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'webhook' => 'Webhook',
                        'callback' => 'Callback',
                        'ajax' => 'AJAX',
                        'manual' => 'Manual',
                        'legacy' => 'Legacy',
                        default => '-',
                    })
                    ->toggleable(),

                TextColumn::make('credential.name')
                    ->label('Credential')
                    ->toggleable(),

                TextColumn::make('transaction_time')
                    ->label('Transaction Time')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('transaction_status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'capture' => 'Capture',
                        'settlement' => 'Settlement',
                        'deny' => 'Deny',
                        'cancel' => 'Cancel',
                        'expire' => 'Expire',
                    ]),

                Tables\Filters\SelectFilter::make('payment_type')
                    ->label('Payment Type')
                    ->options([
                        'credit_card' => 'Credit Card',
                        'bank_transfer' => 'Bank Transfer',
                        'echannel' => 'E-Channel',
                        'gopay' => 'GoPay',
                        'qris' => 'QRIS',
                        'shopeepay' => 'ShopeePay',
                    ]),

                Tables\Filters\SelectFilter::make('midtrans_credential_id')
                    ->label('Credential')
                    ->relationship('credential', 'name'),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    // Tidak ada bulk action untuk keamanan
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
