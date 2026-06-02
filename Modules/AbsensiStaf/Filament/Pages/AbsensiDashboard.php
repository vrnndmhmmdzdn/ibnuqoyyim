<?php

namespace Modules\AbsensiStaf\Filament\Pages;

use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Modules\AbsensiStaf\Models\AbsensiStaf;
use Modules\AbsensiStaf\Models\HariLibur;
use Modules\Guru\Models\Guru;
use Carbon\Carbon;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AbsensiDashboard extends Page
{
    protected string $view = 'absensi-staf::filament.pages.absensi-dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;
    protected static ?string $navigationLabel = 'Dashboard Absensi';
    protected static string|UnitEnum|null $navigationGroup = 'Absensi';
    protected static ?int $navigationSort = 2;

    // Filter
    public string $tanggal_filter = '';

    public function mount(): void
    {
        $this->tanggal_filter = today()->format('Y-m-d');
    }

    #[Computed]
    public function tanggalDipilih(): Carbon
    {
        return Carbon::parse($this->tanggal_filter);
    }

    #[Computed]
    public function totalStaf(): int
    {
        return Guru::count();
    }

    #[Computed]
    public function isLibur(): bool
    {
        $tgl = $this->tanggalDipilih;
        if ($tgl->dayOfWeek === 0) return true;
        return HariLibur::isLibur($tgl);
    }

    #[Computed]
    public function rekap(): array
    {
        $tgl = $this->tanggal_filter;

        $absensi = AbsensiStaf::whereDate('tanggal', $tgl)->get();

        $hadir     = $absensi->whereIn('status', ['hadir'])->count();
        $terlambat = $absensi->where('status', 'terlambat')->count();
        $izin      = $absensi->where('status', 'izin')->count();
        $sakit     = $absensi->where('status', 'sakit')->count();
        $alpha     = $absensi->where('status', 'alpha')->count();
        $belumAbsen = $this->totalStaf - $absensi->count();

        return compact('hadir', 'terlambat', 'izin', 'sakit', 'alpha', 'belumAbsen');
    }

    #[Computed]
    public function daftarStaf(): \Illuminate\Support\Collection
    {
        $tgl     = $this->tanggal_filter;
        $gurus   = Guru::orderBy('name')->get();
        $absensi = AbsensiStaf::whereDate('tanggal', $tgl)
            ->get()
            ->keyBy('guru_id');

        return $gurus->map(function (Guru $guru) use ($absensi) {
            $abs = $absensi->get($guru->id);
            return (object) [
                'guru'        => $guru,
                'absensi'     => $abs,
                'status'      => $abs?->status ?? 'belum',
                'clock_in'    => $abs?->clock_in_at?->format('H:i') ?? '-',
                'clock_out'   => $abs?->clock_out_at?->format('H:i') ?? '-',
                'telat'       => $abs?->telat ?? 0,
                'durasi'      => $abs?->durasi ?? '-',
            ];
        });
    }

    #[Computed]
    public function grafikMingguan(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl = Carbon::today()->subDays($i);
            if ($tgl->dayOfWeek === 0) continue;

            $absensi = AbsensiStaf::whereDate('tanggal', $tgl)->get();
            $data[]  = [
                'hari'      => $tgl->locale('id')->translatedFormat('D d/m'),
                'hadir'     => $absensi->whereIn('status', ['hadir', 'terlambat'])->count(),
                'tidak'     => $this->totalStaf - $absensi->whereIn('status', ['hadir', 'terlambat'])->count(),
            ];
        }
        return $data;
    }
}