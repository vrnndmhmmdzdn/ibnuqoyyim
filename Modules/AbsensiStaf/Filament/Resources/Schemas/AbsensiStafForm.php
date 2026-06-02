<?php

namespace Modules\AbsensiStaf\Filament\Resources\Schemas;

use Modules\AbsensiStaf\Models\AbsensiStaf;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AbsensiStafForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_acara')
                    ->label('Nama Acara')
                    ->required()
                    ->maxLength(255),
                TextInput::make('kegiatan')
                    ->label('Kegiatan')
                    ->required()
                    ->maxLength(255),
                Select::make('kategori')
                    ->label('Jenis Kegiatan')
                    ->options([
                        'Akademik'     => 'Akademik',
                        'Non-Akademik' => 'Non-Akademik',
                        'Ujian'        => 'Ujian',
                        'Libur'        => 'Libur',
                    ])
                    ->required()
                    ->native(false),
                Select::make('subject')
                    ->label('Ditujukan Untuk')
                    ->options(AbsensiStaf::SUBJECTS)
                    ->required()
                    ->native(false),
                Select::make('tahun_ajaran')
                    ->label('Tahun Ajaran')
                    ->options(fn() =>
                        \Modules\TahunAjaran\Models\TahunAjaran::pluck('tahun_ajaran', 'tahun_ajaran')
                    )
                    ->required(),        
                DateTimePicker::make('jam_mulai')
                    ->label('Waktu Mulai')
                    ->required()
                    ->seconds(false),   
                DateTimePicker::make('jam_selesai')
                    ->label('Waktu Selesai')
                    ->required()
                    ->seconds(false)
                    ->after('jam_mulai'),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(3)
                    ->columnSpanFull()
                    ->placeholder('Catatan tambahan untuk Absensi Staf ini...'),
            ]);
    }
}
