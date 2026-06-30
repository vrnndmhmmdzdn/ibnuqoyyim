<?php

namespace Modules\Midtrans\Filament\Resources\MidtransCredentialResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class MidtransCredentialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Konfigurasi')
                            ->required()
                            ->maxLength(255)
                            ->default('Default')
                            ->helperText('Nama untuk identifikasi credential ini'),

                        Forms\Components\Select::make('environment')
                            ->label('Environment')
                            ->options([
                                'sandbox' => 'Sandbox (Testing)',
                                'production' => 'Production (Live)',
                            ])
                            ->required()
                            ->default('sandbox')
                            ->live()
                            ->helperText('Pilih environment Midtrans'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(false)
                            ->helperText('Hanya satu credential yang bisa aktif per environment'),
                    ])
                    ->columns(3),

                Section::make('API Credentials')
                    ->description('Masukkan credential dari Midtrans Dashboard')
                    ->schema([
                        Forms\Components\TextInput::make('merchant_id')
                            ->label('Merchant ID')
                            ->maxLength(255)
                            ->password()
                            ->helperText('Merchant ID dari Midtrans')
                            ->disabled(fn ($record) => config('midtrans.demo_mode') && $record !== null && !empty($record->getRawOriginal('merchant_id')))
                            ->dehydrated(),

                        Forms\Components\TextInput::make('client_key')
                            ->label('Client Key')
                            ->password()
                            ->revealable()
                            ->maxLength(255)
                            ->helperText('Client Key dari Midtrans (akan dienkripsi)')
                            ->disabled(fn ($record) => config('midtrans.demo_mode') && $record !== null && !empty($record->getRawOriginal('client_key')))
                            ->dehydrated(),

                        Forms\Components\TextInput::make('server_key')
                            ->label('Server Key')
                            ->password()
                            ->revealable()
                            ->maxLength(255)
                            ->helperText('Server Key dari Midtrans (akan dienkripsi)')
                            ->disabled(fn ($record) => config('midtrans.demo_mode') && $record !== null && !empty($record->getRawOriginal('server_key')))
                            ->dehydrated(),

                        Forms\Components\Placeholder::make('credential_notice')
                            ->label('')
                            ->content('⚠️ Karena ini adalah web demo, kami kunci credentialnya. Jika Anda sudah membeli template ini, bisa mengubahnya di file konfigurasi.')
                            ->visible(fn ($record) => config('midtrans.demo_mode') && $record !== null && !empty($record->getRawOriginal('server_key'))),
                    ])
                    ->columns(1),

                Section::make('Pengaturan Keamanan')
                    ->schema([
                        Forms\Components\Toggle::make('is_sanitized')
                            ->label('Enable Sanitization')
                            ->default(true)
                            ->helperText('Filter input dari karakter berbahaya'),

                        Forms\Components\Toggle::make('is_3ds')
                            ->label('Enable 3D Secure')
                            ->default(true)
                            ->helperText('Aktifkan verifikasi 3D Secure untuk keamanan tambahan'),
                    ])
                    ->columns(2),

                Section::make('URL Configuration')
                    ->description('Konfigurasi URL untuk callback dan redirect')
                    ->schema([
                        Forms\Components\TextInput::make('notification_url')
                            ->label('Notification URL')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://yourdomain.com/midtrans/notification')
                            ->helperText('URL untuk menerima notifikasi dari Midtrans'),

                        Forms\Components\TextInput::make('finish_url')
                            ->label('Finish URL')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://yourdomain.com/payment/finish')
                            ->helperText('URL ketika pembayaran selesai'),

                        Forms\Components\TextInput::make('unfinish_url')
                            ->label('Unfinish URL')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://yourdomain.com/payment/unfinish')
                            ->helperText('URL ketika pembayaran belum selesai'),

                        Forms\Components\TextInput::make('error_url')
                            ->label('Error URL')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://yourdomain.com/payment/error')
                            ->helperText('URL ketika terjadi error'),
                    ])
                    ->columns(2),

                Section::make('Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->helperText('Catatan tambahan untuk credential ini'),
                    ]),
            ])->columns(1);
    }
}
