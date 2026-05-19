<?php

namespace Modules\MataPelajaran\Filament\Resources\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\MataPelajaran\Models\MataPelajaran;

class MataPelajaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('pelajaran')
                    ->label('Nama Mata Pelajaran')
                    ->required()
                    ->maxLength(255),

                Select::make('kategori')
                    ->label('Kategori')
                    ->options([
                        'Umum' => 'Umum',
                        'Keagamaan' => 'Keagamaan',
                        'Ekstrakurikuler' => 'Ekstrakurikuler',
                    ])
                    ->required()
                    ->native(false),

                Toggle::make('is_aktif')
                    ->label('Aktif')
                    ->helperText('Nonaktifkan jika mata pelajaran tidak digunakan')
                    ->onColor('success')
                    ->offColor('gray')
                    ->default(true),
            ]);
    }
}