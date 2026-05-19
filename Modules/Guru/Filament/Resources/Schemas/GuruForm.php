<?php

namespace Modules\Guru\Filament\Resources\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Date;

class GuruForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('telephone')
                    ->label('No. Telepon')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                DatePicker::make('tanggal_masuk')
                    ->label('Tanggal Masuk')
                    ->required(),
            ]);
    }
}
