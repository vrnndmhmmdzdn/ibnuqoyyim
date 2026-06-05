<?php

namespace Modules\AbsensiStaf\Filament\Resources\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HariLiburForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('tanggal')
                ->label('Tanggal')
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('keterangan')
                ->label('Keterangan')
                ->placeholder('Contoh: HUT Kemerdekaan RI')
                ->required()
                ->maxLength(255),

            Toggle::make('is_aktif')
                ->label('Aktif')
                ->helperText('Nonaktifkan jika ingin mengabaikan hari libur ini')
                ->onColor('success')
                ->offColor('gray')
                ->default(true),
        ]);
    }
}