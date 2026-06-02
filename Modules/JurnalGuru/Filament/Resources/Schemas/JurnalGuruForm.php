<?php

namespace Modules\JurnalGuru\Filament\Resources\Schemas;

use Modules\JurnalGuru\Models\JurnalGuru;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JurnalGuruForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Informasi Mengajar')->columns(2)->schema([
                Select::make('guru_id')
                    ->label('Guru')
                    ->relationship(name: 'guru', titleAttribute: 'name')
                    ->required()
                    ->native(false),

                Select::make('kelas_id')
                    ->label('Kelas')
                    ->relationship(name: 'kelas', titleAttribute: 'nama_kelas')
                    ->required()
                    ->native(false),

                Select::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->relationship(name: 'mataPelajaran', titleAttribute: 'pelajaran')
                    ->required()
                    ->native(false),

                Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->relationship(name: 'tahunAjaran', titleAttribute: 'tahun_ajaran')
                    ->required()
                    ->native(false)
                    ->default(
                        fn() => \Modules\TahunAjaran\Models\TahunAjaran::where('is_aktif', true)
                            ->first()?->id
                    ),

                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->default(today()),

                TextInput::make('pertemuan_ke')
                    ->label('Pertemuan Ke')
                    ->numeric()
                    ->minValue(1)
                    ->nullable(),

                TimePicker::make('jam_mulai')
                    ->label('Jam Mulai')
                    ->required()
                    ->seconds(false),

                TimePicker::make('jam_selesai')
                    ->label('Jam Selesai')
                    ->required()
                    ->seconds(false)
                    ->after('jam_mulai'),
            ]),

            Section::make('Detail Pembelajaran')->schema([
                TextInput::make('materi')
                    ->label('Materi')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('kompetensi_dasar')
                    ->label('Kompetensi Dasar')
                    ->required()
                    ->rows(2)
                    ->columnSpanFull(),

                Textarea::make('deskripsi_kegiatan')
                    ->label('Deskripsi Kegiatan')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                Select::make('metode_pembelajaran')
                    ->label('Metode Pembelajaran')
                    ->options(JurnalGuru::METODE)
                    ->required()
                    ->native(false),

                TextInput::make('media_pembelajaran')
                    ->label('Media Pembelajaran')
                    ->placeholder('Contoh: Proyektor, Papan Tulis')
                    ->nullable(),
            ])->columns(2),

            Section::make('Kehadiran & Evaluasi')->columns(2)->schema([
                TextInput::make('jumlah_hadir')
                    ->label('Jumlah Hadir')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                TextInput::make('jumlah_tidak_hadir')
                    ->label('Jumlah Tidak Hadir')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                Select::make('capaian')
                    ->label('Capaian Pembelajaran')
                    ->options(JurnalGuru::CAPAIAN)
                    ->required()
                    ->native(false)
                    ->default('tercapai'),

                Select::make('status')
                    ->label('Status')
                    ->options(JurnalGuru::STATUS)
                    ->required()
                    ->native(false)
                    ->default('draft'),

                Textarea::make('tindak_lanjut')
                    ->label('Tindak Lanjut')
                    ->rows(2)
                    ->nullable()
                    ->columnSpanFull(),

                Textarea::make('catatan')
                    ->label('Catatan')
                    ->rows(2)
                    ->nullable()
                    ->columnSpanFull(),
            ]),

        ]);
    }
}