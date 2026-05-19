<?php

namespace Modules\JadwalPelajaran\Filament\Resources\Schemas;

use Modules\JadwalPelajaran\Models\JadwalPelajaran;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class JadwalPelajaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->relationship(
                        name: 'mataPelajaran',       // Nama fungsi relasi (method) yang ada di Model Anda
                        titleAttribute: 'pelajaran' // Kolom dari tabel kelas yang mau dipajang (X-A, X-B, dll)
                    )
                    ->required()
                    ->native(false),
                    
                Select::make('kelas_id')
                    ->label('Kelas')
                    ->relationship(
                        name: 'kelas',       // Nama fungsi relasi (method) yang ada di Model Anda
                        titleAttribute: 'nama_kelas' // Kolom dari tabel kelas yang mau dipajang (X-A, X-B, dll)
                    )
                    ->required()
                    ->native(false),
                    
                    TimePicker::make('jam_mulai')
                    ->label('Waktu Mulai')
                    ->required()
                    ->seconds(false),
                    
                    TimePicker::make('jam_selesai')
                    ->label('Waktu Selesai')
                    ->required()
                    ->seconds(false)
                    ->after('jam_mulai'),
                    
                    Select::make('hari')
                        ->label('Hari')
                        ->options(JadwalPelajaran::HARI)
                        ->required()
                        ->native(false),
                        
                    Select::make('guru_id')
                        ->label('Guru Pengajar')
                        ->relationship(
                            name: 'guru',       // Nama fungsi relasi (method) yang ada di Model Anda
                            titleAttribute: 'name' // Kolom dari tabel kelas yang mau dipajang (X-A, X-B, dll)
                        )
                        ->required()
                        ->native(false),
                        
                    Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->relationship(
                        name: 'tahunAjaran',       // Nama fungsi relasi (method) yang ada di Model Anda
                        titleAttribute: 'tahun_ajaran' // Kolom dari tabel kelas yang mau dipajang (X-A, X-B, dll)
                    )
                    ->required()
                    ->native(false),

                
                // Textarea::make('notes')
                //     ->label('Catatan')
                //     ->rows(3)
                //     ->columnSpanFull()
                //     ->placeholder('Catatan tambahan untuk jadwal ini...'),
            ]);
    }
}
