<?php

namespace Modules\TahunAjaran\Filament\Resources\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Date;

class TahunAjaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tahun_ajaran')
                    ->label('Tahun Ajaran')
                    ->required(),
                DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->required(),
                DatePicker::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->required(),
                Toggle::make('is_aktif')
                    ->label('Tahun Ajaran Aktif')
                    ->helperText('Hanya satu tahun ajaran yang bisa aktif')
                    ->onColor('success')
                    ->offColor('gray'),
            ]);
    }
}
