<?php

namespace Modules\Midtrans\Filament\Resources\MidtransTransactionResource\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Modules\Midtrans\Models\MidtransCredential;

class MidtransTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sample Transaction (Demo)')
                    ->description('Buat sample transaction untuk testing Midtrans payment gateway')
                    ->schema([
                        Select::make('midtrans_credential_id')
                            ->label('Midtrans Credential')
                            ->options(MidtransCredential::where('is_active', true)->pluck('name', 'id'))
                            ->default(fn() => MidtransCredential::getActiveCredential()?->id)
                            ->required()
                            ->helperText('Pilih credential Midtrans yang akan digunakan')
                            ->hiddenOn('edit'),

                        TextInput::make('order_id')
                            ->label('Order ID (Optional)')
                            ->placeholder('Kosongkan untuk auto-generate')
                            ->maxLength(255)
                            ->helperText('Order ID akan di-generate otomatis jika dikosongkan')
                            ->hiddenOn('edit'),

                        TextInput::make('gross_amount')
                            ->label('Total Amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(1)
                            ->default(100000)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Total amount yang akan dibayar (dihitung otomatis dari items)')
                            ->reactive()
                            ->hiddenOn('edit'),
                    ])
                    ->columns(2)
                    ->hiddenOn('edit'),

                Section::make('Customer Information')
                    ->description('Informasi customer untuk transaction')
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('Customer Name')
                            ->required()
                            ->default('Sample Customer')
                            ->maxLength(255)
                            ->hiddenOn('edit'),

                        TextInput::make('customer_email')
                            ->label('Customer Email')
                            ->email()
                            ->required()
                            ->default('customer@example.com')
                            ->maxLength(255)
                            ->hiddenOn('edit'),

                        TextInput::make('customer_phone')
                            ->label('Customer Phone')
                            ->tel()
                            ->required()
                            ->default('08123456789')
                            ->maxLength(20)
                            ->hiddenOn('edit'),
                    ])
                    ->columns(3)
                    ->hiddenOn('edit'),

                Section::make('Item Details')
                    ->description('Daftar produk/item yang akan dibeli (gross amount akan dihitung otomatis)')
                    ->schema([
                        Repeater::make('items')
                            ->label('Products')
                            ->table([
                                TableColumn::make('Product Name'),
                                TableColumn::make('Qty')->width('100px'),
                                TableColumn::make('Price')->width('150px'),
                                TableColumn::make('Subtotal')->width('150px'),
                            ])
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->default('Sample Product'),

                                TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $quantity = (float) ($state ?? 0);
                                        $price = (float) ($get('price') ?? 0);
                                        $set('subtotal', $quantity * $price);
                                    }),

                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(100000)
                                    ->minValue(0)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $price = (float) ($state ?? 0);
                                        $quantity = (float) ($get('quantity') ?? 0);
                                        $set('subtotal', $quantity * $price);
                                    }),

                                TextInput::make('subtotal')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(function (callable $get) {
                                        $quantity = (float) ($get('quantity') ?? 1);
                                        $price = (float) ($get('price') ?? 100000);
                                        return $quantity * $price;
                                    }),
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('Add Product')
                            ->reorderable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Calculate total gross amount from all items
                                $total = 0;
                                if (\is_array($state)) {
                                    foreach ($state as $item) {
                                        $quantity = (float) ($item['quantity'] ?? 0);
                                        $price = (float) ($item['price'] ?? 0);
                                        $total += $quantity * $price;
                                    }
                                }
                                $set('gross_amount', $total);
                            })
                            ->hiddenOn('edit'),
                    ])
                    ->hiddenOn('edit'),

                // View mode fields (for edit/view pages)
                Section::make('Transaction Details')
                    ->schema([
                        TextInput::make('order_id')
                            ->label('Order ID')
                            ->disabled()
                            ->visibleOn('edit'),

                        TextInput::make('transaction_id')
                            ->label('Transaction ID')
                            ->disabled()
                            ->visibleOn('edit'),

                        TextInput::make('gross_amount')
                            ->label('Amount')
                            ->disabled()
                            ->prefix('Rp')
                            ->visibleOn('edit'),

                        TextInput::make('payment_type')
                            ->label('Payment Type')
                            ->disabled()
                            ->visibleOn('edit'),

                        TextInput::make('transaction_status')
                            ->label('Status')
                            ->disabled()
                            ->visibleOn('edit'),

                        TextInput::make('fraud_status')
                            ->label('Fraud Status')
                            ->disabled()
                            ->visibleOn('edit'),
                    ])
                    ->columns(2)
                    ->visibleOn('edit'),
            ])->columns(1);
    }
}
