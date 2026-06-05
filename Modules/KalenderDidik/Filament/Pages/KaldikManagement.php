<?php

namespace Modules\KalenderDidik\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Modules\KalenderDidik\Models\Kaldik;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;

class KaldikManagement extends Page
{
    protected string $view = 'kalender-didik::filament.pages.kaldik-management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AdjustmentsHorizontal;
    protected static ?string $navigationLabel = 'Manajemen Kaldik';
    protected static string|UnitEnum|null $navigationGroup = 'Kalender Pendidikan';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'kaldik-management';

    public function getTitle(): string
    {
        return 'Manajemen Kalender Akademik';
    }

    public function getHeading(): string
    {
        return 'Manajemen Kalender Akademik';
    }

    public function getSubheading(): ?string
    {
        return 'Kelola dan pantau seluruh kegiatan akademik sekolah';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('tambah_kegiatan')
                ->label('Tambah Kegiatan')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->modalWidth('2xl')
                ->form([
                    TextInput::make('nama_acara')
                        ->label('Nama Acara')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('kegiatan')
                        ->label('Kegiatan')
                        ->placeholder('Contoh: Ujian tulis semua mata pelajaran')
                        ->required()
                        ->maxLength(255),

                    Select::make('kategori')
                        ->label('Kategori')
                        ->options(Kaldik::KATEGORI)
                        ->required()
                        ->native(false),

                    Select::make('subject')
                        ->label('Ditujukan Untuk')
                        ->options(Kaldik::SUBJECTS)
                        ->required()
                        ->native(false),

                    Select::make('tahun_ajaran')
                        ->label('Tahun Ajaran')
                        ->options(fn() =>
                            \Modules\TahunAjaran\Models\TahunAjaran::pluck('tahun_ajaran', 'tahun_ajaran')
                        )
                        ->default(
                            \Modules\TahunAjaran\Models\TahunAjaran::where('is_aktif', true)
                                ->first()?->tahun_ajaran
                        )
                        ->required(),

                    DateTimePicker::make('jam_mulai')
                        ->label('Tanggal Mulai')
                        ->required()
                        ->seconds(false),

                    DateTimePicker::make('jam_selesai')
                        ->label('Tanggal Selesai')
                        ->required()
                        ->seconds(false)
                        ->after('jam_mulai'),

                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(3)
                        ->nullable(),
                ])
                ->action(function (array $data) {
                    Kaldik::create($data);

                    Notification::make()
                        ->title('Kegiatan berhasil ditambahkan')
                        ->success()
                        ->send();
                }),
        ];
    }
}