<?php

namespace Modules\Midtrans\Filament\Resources\MidtransTransactionResource\Infolists;

use Filament\Infolists;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class MidtransTransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transaction Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('order_id')
                            ->label('Order ID')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('transaction_id')
                            ->label('Transaction ID')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('gross_amount')
                            ->label('Amount')
                            ->money('IDR'),

                        Infolists\Components\TextEntry::make('payment_type')
                            ->label('Payment Type'),

                        Infolists\Components\TextEntry::make('transaction_status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'pending' => 'warning',
                                'capture', 'settlement' => 'success',
                                'deny', 'cancel', 'expire' => 'danger',
                                default => 'gray',
                            }),

                        Infolists\Components\TextEntry::make('fraud_status')
                            ->label('Fraud Status')
                            ->badge()
                            ->color(fn(?string $state): string => match ($state) {
                                'accept' => 'success',
                                'challenge' => 'warning',
                                'deny' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn(?string $state): string => match ($state) {
                                'accept' => '✅ Aman (No Fraud)',
                                'challenge' => '⚠️ Perlu Review',
                                'deny' => '❌ Terdeteksi Fraud',
                                default => '-',
                            }),

                        Infolists\Components\TextEntry::make('transaction_time')
                            ->label('Transaction Time')
                            ->dateTime('d M Y H:i:s'),

                        Infolists\Components\TextEntry::make('updated_via')
                            ->label('Updated Via')
                            ->badge()
                            ->color(fn(?string $state): string => match ($state) {
                                'webhook' => 'success',
                                'callback' => 'info',
                                'ajax' => 'warning',
                                'manual', 'legacy' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn(?string $state): string => match ($state) {
                                'webhook' => 'Webhook',
                                'callback' => 'Callback',
                                'ajax' => 'AJAX',
                                'manual' => 'Manual',
                                'legacy' => 'Legacy',
                                default => '-',
                            }),

                        Infolists\Components\TextEntry::make('status_updated_at')
                            ->label('Status Updated At')
                            ->dateTime('d M Y H:i:s'),

                        Infolists\Components\TextEntry::make('snap_token')
                            ->label('Snap Token')
                            ->copyable()
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('snap_url')
                            ->label('Snap URL')
                            ->copyable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Customer Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label('Customer')
                            ->formatStateUsing(function ($state, $record) {
                                $customerDetails = $record->customer_details;

                                if (empty($customerDetails))
                                    return '-';

                                $formatted = [];

                                // Direct access untuk keys yang diketahui
                                if (isset($customerDetails['first_name'])) {
                                    $formatted[] = '<strong>Name:</strong> ' . $customerDetails['first_name'];
                                }
                                if (isset($customerDetails['email'])) {
                                    $formatted[] = '<strong>Email:</strong> ' . $customerDetails['email'];
                                }
                                if (isset($customerDetails['phone'])) {
                                    $formatted[] = '<strong>Phone:</strong> ' . $customerDetails['phone'];
                                }

                                // Jika tidak ada data yang dikenal, tampilkan semua
                                if (empty($formatted) && \is_array($customerDetails)) {
                                    foreach ($customerDetails as $key => $value) {
                                        if (!\is_numeric($key) && !empty($value)) {
                                            $formatted[] = '<strong>' . ucwords(\str_replace('_', ' ', $key)) . ':</strong> ' . $value;
                                        }
                                    }
                                }

                                return $formatted ? \implode('<br>', $formatted) : '-';
                            })
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Item Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label('Products')
                            ->formatStateUsing(function ($state, $record) {
                                $itemDetails = $record->item_details;

                                if (empty($itemDetails))
                                    return '-';

                                $items = [];

                                if (\is_array($itemDetails)) {
                                    foreach ($itemDetails as $item) {
                                        // Pastikan $item adalah array dengan keys yang benar
                                        if (\is_array($item)) {
                                            $name = $item['name'] ?? $item['id'] ?? 'Unknown Item';
                                            $quantity = $item['quantity'] ?? 1;
                                            $price = $item['price'] ?? 0;
                                            $total = $price * $quantity;

                                            $items[] = \sprintf(
                                                '<strong>%s</strong> x %s @ Rp %s = Rp %s',
                                                $name,
                                                $quantity,
                                                \number_format($price, 0, ',', '.'),
                                                \number_format($total, 0, ',', '.')
                                            );
                                        }
                                    }
                                }

                                return $items ? \implode('<br>', $items) : '-';
                            })
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Raw Response')
                    ->schema([
                        Infolists\Components\TextEntry::make('raw_response')
                            ->label('')
                            ->formatStateUsing(function ($state) {
                                if (!$state)
                                    return '-';
                                return '<pre>' . json_encode(json_decode($state), JSON_PRETTY_PRINT) . '</pre>';
                            })
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
