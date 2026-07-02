<?php

namespace Modules\AbsensiStaf\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Livewire\Attributes\Computed;
use Maatwebsite\Excel\Facades\Excel;
use Modules\AbsensiStaf\Exports\AbsensiPersonalExport;
use Modules\AbsensiStaf\Exports\AbsensiRekkapExport;
use Modules\Guru\Models\Guru;
use Carbon\Carbon;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportAbsensi extends Page
{
    protected string $view = 'absensi-staf::filament.pages.export-absensi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;
    protected static ?string $navigationLabel = 'Export Absensi';
    protected static string|UnitEnum|null $navigationGroup = 'Absensi';
    protected static ?int $navigationSort = 7;

    // Filter
    public string $bulan = '';
    public string $tahun = '';
    public string $tipe  = 'semua'; // 'semua' atau 'personal'
    public ?int $guru_id  = null;

    public function mount(): void
    {
        $this->bulan = now()->format('m');
        $this->tahun = now()->format('Y');
    }

    #[Computed]
    public function guruList(): array
    {
        return Guru::orderBy('name')->pluck('name', 'id')->toArray();
    }

    #[Computed]
    public function bulanList(): array
    {
        return [
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
        ];
    }

    #[Computed]
    public function tahunList(): array
    {
        $tahun = now()->year;
        $list  = [];
        for ($y = $tahun; $y >= $tahun - 2; $y--) {
            $list[$y] = (string) $y;
        }
        return $list;
    }

    #[Computed]
    public function namaBulan(): string
    {
        return Carbon::create($this->tahun, $this->bulan)
            ->locale('id')
            ->translatedFormat('F Y');
    }

    public function export(): BinaryFileResponse
    {
        if ($this->tipe === 'personal') {
            if (!$this->guru_id) {
                Notification::make()
                    ->title('Pilih staf terlebih dahulu')
                    ->warning()
                    ->send();
                return back();
            }

            $guru     = Guru::find($this->guru_id);
            $fileName = 'absensi_' . str_replace(' ', '_', strtolower($guru?->name ?? 'staf'))
                . '_' . $this->bulan . '_' . $this->tahun . '.xlsx';

            return Excel::download(
                new AbsensiPersonalExport($this->guru_id, $this->bulan, $this->tahun),
                $fileName
            );
        }

        // Export semua staf
        $fileName = 'rekap_absensi_' . $this->bulan . '_' . $this->tahun . '.xlsx';

        return Excel::download(
            new AbsensiRekkapExport($this->bulan, $this->tahun),
            $fileName
        );
    }
    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }
}