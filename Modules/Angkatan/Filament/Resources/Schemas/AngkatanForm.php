<?php

namespace Modules\Angkatan\Filament\Resources\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Date;
use Modules\TahunAjaran\Models\TahunAjaran;

class AngkatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_angkatan')
                    ->label('Nama Angkatan')
                    ->required(),
                TextInput::make('angkatan_ke')
                    ->label('Angkatan Ke')
                    ->unique(ignoreRecord: true)
                    ->numeric()
                    ->required(),
                TextInput::make('tahun_mulai')
                    ->label('Tahun Mulai')
                    ->numeric()
                    // ->options(
                    //     \Modules\TahunAjaran\Models\TahunAjaran::pluck('tahun_ajaran', 'id')
                    // )
                    // ->native(false)
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'lulus' => 'Lulus',
                    ])
                    ->default('aktif')
                    ->native(false)
                    ->required(),
            ]);
    }
}
