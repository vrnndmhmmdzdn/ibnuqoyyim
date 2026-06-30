<?php

namespace Modules\MutabaahTahfidz\Filament\Pages;

use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Computed;
use Modules\Kelas\Models\Kelas;
use Modules\MutabaahTahfidz\Models\MutabaahRecord;
use Modules\Siswa\Models\Siswa;
use UnitEnum;

class MutabaahStatistik extends Page
{
    protected string $view = 'mutabaah-tahfidz::filament.pages.mutabaah-statistik';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;
    protected static ?string $navigationLabel  = 'Statistik';
    protected static string|UnitEnum|null $navigationGroup = 'Mutabaah Tahfidz';
    protected static ?int $navigationSort      = 4;

    public ?int    $kelas_id  = null;
    public string  $periode   = 'minggu'; // minggu | bulan | semua

    #[Computed]
    public function kelasList(): array
    {
        return Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id')->toArray();
    }

    #[Computed]
    public function statsData(): array
    {
        if (!$this->kelas_id) return [];

        // Ambil siswa dari pivot dulu, lalu filter records by siswa_id
        $siswaList = Siswa::whereHas('kelas', fn ($q) =>
            $q->where('kelas.id', $this->kelas_id)
              ->where('kelas_pivot.is_aktif', true)
              ->whereNull('kelas_pivot.deleted_at')
        )->orderBy('nama_lengkap')->get();

        if ($siswaList->isEmpty()) return [];

        $siswaIds = $siswaList->pluck('id');

        $query = MutabaahRecord::whereIn('siswa_id', $siswaIds);

        $query = match ($this->periode) {
            'minggu' => (clone $query)->whereBetween('tanggal', [
                now()->startOfWeek(Carbon::MONDAY),
                now()->endOfWeek(Carbon::SUNDAY),
            ]),
            'bulan'  => (clone $query)->whereMonth('tanggal', now()->month)
                                       ->whereYear('tanggal', now()->year),
            default  => clone $query,
        };

        $records = $query->with(['surah', 'siswa'])->get();
        $grouped = $records->groupBy('siswa_id');

        $rows = [];
        foreach ($siswaList as $siswa) {
            $recs    = $grouped[$siswa->id] ?? collect();
            $setoran = $recs->whereIn('status', MutabaahRecord::STATUS_NEEDS_SURAH);

            $rows[] = [
                'siswa'      => $siswa,
                'totalAyat'  => $setoran->sum('jumlah_ayat'),
                'hariSetor'  => $setoran->count(),
                'mumtaz'     => $recs->where('nilai', 'mumtaz')->count(),
                'rasib'      => $recs->where('nilai', 'rasib')->count(),
                'tidakMasuk' => $recs->where('status', 'tidak_masuk')->count(),
                'tidakSetor' => $recs->where('status', 'tidak_setoran')->count(),
                'lastRecord' => $recs->sortByDesc('tanggal')->first(),
            ];
        }

        // Sort by total ayat descending
        usort($rows, fn ($a, $b) => $b['totalAyat'] <=> $a['totalAyat']);

        $maxAyat = max(1, collect($rows)->max('totalAyat'));

        // Students with 0 setoran (alert)
        $alerts = array_filter($rows, fn ($r) => $r['hariSetor'] === 0);

        return [
            'rows'    => $rows,
            'maxAyat' => $maxAyat,
            'alerts'  => array_values($alerts),
            'podium'  => array_slice($rows, 0, 3),
            'total'   => [
                'siswa'  => count($rows),
                'ayat'   => array_sum(array_column($rows, 'totalAyat')),
                'setor'  => array_sum(array_column($rows, 'hariSetor')),
                'mumtaz' => array_sum(array_column($rows, 'mumtaz')),
            ],
        ];
    }

    public function updatedKelasId(): void   { unset($this->statsData); }
    public function updatedPeriode(): void   { unset($this->statsData); }
}
