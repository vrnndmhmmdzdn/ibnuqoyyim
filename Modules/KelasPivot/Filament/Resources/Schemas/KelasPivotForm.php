<?php

namespace Modules\KelasPivot\Filament\Resources\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Date;
use Modules\Kelas\Models\Kelas;
use Modules\Siswa\Models\Siswa;
use Modules\TahunAjaran\Models\TahunAjaran;

class KelasPivotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('kelas_id')
                    ->label('Nama Kelas')
                    ->options(\Modules\Kelas\Models\Kelas::pluck('nama_kelas', 'id'))
                    ->required(),
                Select::make('siswa_id')
                    ->label('Siswa')
                    ->options(\Modules\Siswa\Models\Siswa::pluck('nama_lengkap', 'id'))
                    ->unique(ignoreRecord: true)
                    // ->numeric()
                    ->required(),
                Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->options(\Modules\TahunAjaran\Models\TahunAjaran::pluck('tahun_ajaran', 'id'))
                    ->required(),
                Radio::make('is_aktif')
                    ->label('Sedang Aktif?')
                    ->boolean()
                    ->inline()
                    ->options([
                        1 => 'Ya',
                        0 => 'Tidak',
                    ])
            ]);
    }
}
