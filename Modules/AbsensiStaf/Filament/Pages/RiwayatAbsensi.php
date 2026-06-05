<?php

namespace Modules\AbsensiStaf\Filament\Pages;

use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Modules\AbsensiStaf\Models\AbsensiStaf;
use Modules\AbsensiStaf\Models\HariLibur;
use Carbon\Carbon;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class RiwayatAbsensi extends Page
{
    protected string $view = 'absensi-staf::filament.pages.riwayat-absensi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static ?string $navigationLabel = 'Riwayat Absensi';
    protected static string|UnitEnum|null $navigationGroup = 'Absensi';
    protected static ?int $navigationSort = 3;

    // Filter
    public string $bulan  = '';
    public string $tahun  = '';
    public string $status = '';

    public function mount(): void
    {
        $this->bulan = now()->format('m');
        $this->tahun = now()->format('Y');
    }

    #[Computed]
    public function guru()
    {
        return auth()->user()->guru;
    }

    #[Computed]
    public function riwayat(): \Illuminate\Support\Collection
    {
        if (!$this->guru) return collect();

        $query = AbsensiStaf::where('guru_id', $this->guru->id)
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->orderBy('tanggal', 'desc');

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->get();
    }

    #[Computed]
    public function rekapBulan(): array
    {
        $data = $this->riwayat;

        return [
            'hadir'     => $data->where('status', 'hadir')->count(),
            'terlambat' => $data->where('status', 'terlambat')->count(),
            'izin'      => $data->where('status', 'izin')->count(),
            'sakit'     => $data->where('status', 'sakit')->count(),
            'alpha'     => $data->where('status', 'alpha')->count(),
            'total_hari'=> $data->count(),
            'total_durasi' => $this->hitungTotalDurasi($data),
        ];
    }

    private function hitungTotalDurasi(\Illuminate\Support\Collection $data): string
    {
        $totalMenit = $data->sum(function ($abs) {
            if (!$abs->clock_in_at || !$abs->clock_out_at) return 0;
            return $abs->clock_in_at->diffInMinutes($abs->clock_out_at);
        });

        $jam  = intdiv($totalMenit, 60);
        $menit = $totalMenit % 60;
        return "{$jam}j {$menit}m";
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
        $tahunSekarang = now()->year;
        $list = [];
        for ($y = $tahunSekarang; $y >= $tahunSekarang - 2; $y--) {
            $list[$y] = (string) $y;
        }
        return $list;
    }
}