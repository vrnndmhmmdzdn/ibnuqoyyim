<?php

namespace Modules\Penilaian\Filament\Pages;

use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Modules\Penilaian\Models\PenilaianItem;
use Modules\Penilaian\Models\PenilaianRekap;
use Modules\Kelas\Models\Kelas;
use Modules\MataPelajaran\Models\MataPelajaran;
use Modules\TahunAjaran\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class LaporanNilai extends Page
{
    protected string $view = 'penilaian::filament.pages.laporan-nilai';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;
    protected static ?string $navigationLabel = 'Laporan Nilai';
    protected static string|UnitEnum|null $navigationGroup = 'Penilaian';
    protected static ?int $navigationSort = 3;

    public ?int    $kelas_id          = null;
    public ?int    $tahun_ajaran_id   = null;
    public string  $semester          = '1';
    public ?int    $selected_siswa_id = null;
    public string  $mode              = 'kelas'; // 'kelas' | 'siswa'

    public function mount(): void
    {
        $this->tahun_ajaran_id = TahunAjaran::where('is_aktif', true)->first()?->id;
    }

    #[Computed]
    public function kelasList(): array
    {
        return Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id')->toArray();
    }

    #[Computed]
    public function tahunAjaranList(): array
    {
        return TahunAjaran::orderByDesc('tahun_ajaran')->pluck('tahun_ajaran', 'id')->toArray();
    }

    #[Computed]
    public function siswaList(): \Illuminate\Support\Collection
    {
        if (!$this->kelas_id || !$this->tahun_ajaran_id) return collect();

        return DB::table('siswas')
            ->join('kelas_pivot', 'siswas.id', '=', 'kelas_pivot.siswa_id')
            ->where('kelas_pivot.kelas_id', $this->kelas_id)
            ->where('kelas_pivot.tahun_ajaran_id', $this->tahun_ajaran_id)
            ->where('kelas_pivot.is_aktif', true)
            ->whereNull('kelas_pivot.deleted_at')
            ->whereNull('siswas.deleted_at')
            ->orderBy('siswas.nama_lengkap')
            ->select('siswas.id', 'siswas.nama_lengkap', 'siswas.nis')
            ->get();
    }

    #[Computed]
    public function mapelList(): \Illuminate\Support\Collection
    {
        if (!$this->kelas_id || !$this->tahun_ajaran_id) return collect();

        $mapelIds = PenilaianItem::where('kelas_id', $this->kelas_id)
            ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->where('semester', $this->semester)
            ->whereNull('deleted_at')
            ->distinct()
            ->pluck('mata_pelajaran_id');

        return MataPelajaran::whereIn('id', $mapelIds)->orderBy('pelajaran')->get();
    }

    #[Computed]
    public function rekapData(): array
    {
        if (!$this->kelas_id || !$this->tahun_ajaran_id) return [];

        $siswaIds = $this->siswaList->pluck('id');
        $mapelIds = $this->mapelList->pluck('id');

        $rekaps = PenilaianRekap::whereIn('siswa_id', $siswaIds)
            ->whereIn('mata_pelajaran_id', $mapelIds)
            ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->where('semester', $this->semester)
            ->get();

        $shaped = [];
        foreach ($rekaps as $r) {
            $shaped[$r->siswa_id][$r->mata_pelajaran_id] = $r;
        }
        return $shaped;
    }

    // Ranking siswa berdasarkan rata-rata nilai akhir semua mapel
    #[Computed]
    public function rankingSiswa(): \Illuminate\Support\Collection
    {
        return $this->siswaList->map(function ($siswa) {
            $rekaps = $this->rekapData[$siswa->id] ?? [];
            $nilais = collect($rekaps)->pluck('nilai_akhir')->filter()->values();
            return (object) [
                'siswa'      => $siswa,
                'rata_rata'  => $nilais->isEmpty() ? null : round($nilais->avg(), 1),
                'jumlah_a'   => collect($rekaps)->where('predikat', 'A')->count(),
                'jumlah_b'   => collect($rekaps)->where('predikat', 'B')->count(),
            ];
        })->sortByDesc('rata_rata')->values();
    }

    // Data per siswa terpilih: breakdown NH/NT/PTS/PAS per mapel
    #[Computed]
    public function dataSiswa(): \Illuminate\Support\Collection
    {
        if (!$this->selected_siswa_id) return collect();

        $mapelIds = $this->mapelList->pluck('id');
        $rekaps   = PenilaianRekap::where('siswa_id', $this->selected_siswa_id)
            ->whereIn('mata_pelajaran_id', $mapelIds)
            ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->where('semester', $this->semester)
            ->with('mataPelajaran')
            ->get();

        return $rekaps->sortBy(fn($r) => $r->mataPelajaran?->pelajaran);
    }

    public function updatedKelasId(): void
    {
        $this->reset(['selected_siswa_id']);
        unset($this->siswaList, $this->mapelList, $this->rekapData, $this->rankingSiswa);
    }

    public function generateWaText(): string
    {
        if (!$this->selected_siswa_id) return '';

        $siswa    = $this->siswaList->firstWhere('id', $this->selected_siswa_id);
        $kelas    = Kelas::find($this->kelas_id);
        $ta       = TahunAjaran::find($this->tahun_ajaran_id);
        $lines    = [];
        $lines[]  = "📋 *Laporan Nilai Siswa*";
        $lines[]  = "Nama  : {$siswa?->nama_lengkap}";
        $lines[]  = "Kelas : {$kelas?->nama_kelas}";
        $lines[]  = "TA    : {$ta?->tahun_ajaran} | Semester {$this->semester}";
        $lines[]  = str_repeat('─', 30);

        foreach ($this->dataSiswa as $rekap) {
            $na = $rekap->nilai_akhir !== null ? number_format($rekap->nilai_akhir, 1) : '-';
            $lines[] = "• {$rekap->mataPelajaran?->pelajaran}: *{$na}* ({$rekap->predikat})";
        }

        return implode("\n", $lines);
    }
}