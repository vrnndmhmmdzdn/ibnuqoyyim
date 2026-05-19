<?php

namespace Modules\Siswa\Filament\Resources\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Form Data Siswa')
                    ->tabs([
                        // TAB 1: DATA AKADEMIK UTAMA
                        Tab::make('Data Akademik Utama')
                            ->icon('heroicon-m-academic-cap')
                            ->schema([
                                TextInput::make('nisn')
                                    ->label('NISN')
                                    ->required()
                                    ->numeric()
                                    // ->length(10)
                                    ->unique(ignoreRecord: true),

                                TextInput::make('nis')
                                    ->label('NIS')
                                    ->required()
                                    ->numeric()
                                    // ->maxLength(20)
                                    ->unique(ignoreRecord: true),

                                Select::make('angkatan_id')
                                    ->label('Angkatan')
                                    ->relationship('angkatan', 'angkatan_ke') // Pastikan kolom nama di tabel angkatan sesuai (misal: nama_angkatan atau tahun)
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),

                                Select::make('status_siswa')
                                    ->label('Status Siswa')
                                    ->options([
                                        'aktif' => 'Aktif',
                                        'lulus' => 'Lulus',
                                        'pindah' => 'Pindah',
                                        'drop-out' => 'Drop Out',
                                    ])
                                    ->required()
                                    ->default('aktif'),

                                DatePicker::make('tanggal_masuk')
                                    ->label('Tanggal Masuk')
                                    ->required(),
                            ])->columns(2),

                        // TAB 2: DATA PRIBADI
                        Tab::make('Data Pribadi')
                            ->icon('heroicon-m-user')
                            ->schema([
                                TextInput::make('nama_lengkap')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('nama_panggilan')
                                    ->label('Nama Panggilan')
                                    ->maxLength(255)
                                    ->nullable(),

                                Select::make('jenis_kelamin')
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        'L' => 'Laki-laki',
                                        'P' => 'Perempuan',
                                    ])
                                    ->required(),

                                TextInput::make('tempat_lahir')
                                    ->label('Tempat Lahir')
                                    ->required()
                                    ->maxLength(255),

                                DatePicker::make('tanggal_lahir')
                                    ->label('Tanggal Lahir')
                                    ->required(),

                                TextInput::make('nik')
                                    ->label('NIK')
                                    ->numeric()
                                    // ->length(16)
                                    ->unique(ignoreRecord: true)
                                    ->nullable(),

                                FileUpload::make('foto_siswa')
                                    ->label('Foto Siswa')
                                    ->image()
                                    ->directory('foto-siswa')
                                    ->avatar()
                                    ->nullable(),
                            ])->columns(2),

                        // TAB 3: KONTAK & DOMISILI
                        Tab::make('Kontak & Domisili')
                            ->icon('heroicon-m-map-pin')
                            ->schema([
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->unique(ignoreRecord: true)
                                    ->nullable(),

                                TextInput::make('nomor_hp')
                                    ->label('Nomor HP Siswa')
                                    ->tel()
                                    // ->maxLength(15)
                                    ->nullable(),

                                TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->nullable(),

                                TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->nullable(),

                                Textarea::make('alamat')
                                    ->label('Alamat Lengkap')
                                    ->nullable()
                                    ->columnSpanFull(),
                            ])->columns(2),

                        // TAB 4: DATA ORANG TUA / WALI & MEDIS
                        Tab::make('Keluarga & Tambahan')
                            ->icon('heroicon-m-users')
                            ->schema([
                                TextInput::make('nama_ayah')
                                    ->label('Nama Ayah')
                                    ->maxLength(255)
                                    ->nullable(),

                                TextInput::make('pekerjaan_ayah')
                                    ->label('Pekerjaan Ayah')
                                    ->maxLength(255)
                                    ->nullable(),

                                TextInput::make('nama_ibu')
                                    ->label('Nama Ibu')
                                    ->maxLength(255)
                                    ->nullable(),

                                TextInput::make('pekerjaan_ibu')
                                    ->label('Pekerjaan Ibu')
                                    ->maxLength(255)
                                    ->nullable(),

                                TextInput::make('nama_wali')
                                    ->label('Nama Wali')
                                    ->maxLength(255)
                                    ->nullable(),

                                TextInput::make('nomor_hp_orang_tua')
                                    ->label('Nomor HP Orang Tua / Wali')
                                    ->tel()
                                    // ->maxLength(15)
                                    ->required(),

                                Textarea::make('catatan_medis')
                                    ->label('Catatan Medis')
                                    ->nullable()
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])->columnSpanFull()
            ]);
    }
}
