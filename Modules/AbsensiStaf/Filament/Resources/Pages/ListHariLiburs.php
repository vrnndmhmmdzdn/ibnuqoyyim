<?php

namespace Modules\AbsensiStaf\Filament\Resources\Pages;

use Modules\AbsensiStaf\Filament\Resources\HariLiburResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHariLiburs extends ListRecords
{
    protected static string $resource = HariLiburResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('import_preset')
                ->label('Import Libur Nasional 2025/2026')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Import Hari Libur Nasional')
                ->modalDescription('Ini akan menambahkan daftar hari libur nasional 2025/2026. Data yang sudah ada tidak akan ditimpa.')
                ->action(function () {
                    $liburs = [
                        ['tanggal' => '2025-08-17', 'keterangan' => 'HUT Kemerdekaan RI'],
                        ['tanggal' => '2025-09-05', 'keterangan' => 'Maulid Nabi Muhammad SAW'],
                        ['tanggal' => '2025-10-01', 'keterangan' => 'Hari Kesaktian Pancasila'],
                        ['tanggal' => '2025-11-10', 'keterangan' => 'Hari Pahlawan'],
                        ['tanggal' => '2025-12-25', 'keterangan' => 'Hari Natal'],
                        ['tanggal' => '2026-01-01', 'keterangan' => 'Tahun Baru Masehi'],
                        ['tanggal' => '2026-01-27', 'keterangan' => 'Tahun Baru Imlek'],
                        ['tanggal' => '2026-02-19', 'keterangan' => 'Isra Miraj Nabi Muhammad SAW'],
                        ['tanggal' => '2026-03-09', 'keterangan' => 'Hari Raya Nyepi'],
                        ['tanggal' => '2026-03-28', 'keterangan' => 'Idul Fitri 1447 H'],
                        ['tanggal' => '2026-03-29', 'keterangan' => 'Idul Fitri 1447 H'],
                        ['tanggal' => '2026-03-30', 'keterangan' => 'Cuti Bersama Idul Fitri'],
                        ['tanggal' => '2026-03-31', 'keterangan' => 'Cuti Bersama Idul Fitri'],
                        ['tanggal' => '2026-04-01', 'keterangan' => 'Cuti Bersama Idul Fitri'],
                        ['tanggal' => '2026-04-02', 'keterangan' => 'Hari Paskah'],
                        ['tanggal' => '2026-05-01', 'keterangan' => 'Hari Buruh Internasional'],
                        ['tanggal' => '2026-05-14', 'keterangan' => 'Kenaikan Isa Al-Masih'],
                        ['tanggal' => '2026-05-20', 'keterangan' => 'Hari Waisak'],
                        ['tanggal' => '2026-06-01', 'keterangan' => 'Hari Lahir Pancasila'],
                        ['tanggal' => '2026-06-05', 'keterangan' => 'Idul Adha 1447 H'],
                    ];

                    $imported = 0;
                    foreach ($liburs as $libur) {
                        $exists = \Modules\AbsensiStaf\Models\HariLibur::where('tanggal', $libur['tanggal'])->exists();
                        if (!$exists) {
                            \Modules\AbsensiStaf\Models\HariLibur::create(array_merge($libur, ['is_aktif' => true]));
                            $imported++;
                        }
                    }

                    \Filament\Notifications\Notification::make()
                        ->title("{$imported} hari libur berhasil diimport")
                        ->success()
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}