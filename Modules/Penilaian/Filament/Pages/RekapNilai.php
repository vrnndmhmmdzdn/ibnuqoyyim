<?php

namespace Modules\Penilaian\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Livewire\Attributes\Computed;
use Modules\Penilaian\Models\PenilaianItem;
use Modules\Penilaian\Models\PenilaianRekap;
use Modules\Kelas\Models\Kelas;
use Modules\MataPelajaran\Models\MataPelajaran;
use Modules\TahunAjaran\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class RekapNilai extends Page
{
    protected string $view = 'penilaian::filament.pages.rekap-nilai';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTableCells;
    protected static ?string $navigationLabel = 'Rekap Nilai';
    protected static string|UnitEnum|null $navigationGroup = 'Penilaian';
    protected static ?int $navigationSort = 2;

    public ?int   $kelas_id        = null;
    public ?int   $tahun_ajaran_id = null;
    public string $semester        = '1';

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

    // Daftar mapel yang ada itemnya di kelas + semester ini
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

        return MataPelajaran::whereIn('id', $mapelIds)
            ->orderBy('pelajaran')
            ->get();
    }

    // Siswa di kelas via kelas_pivot
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

    // Reshape rekap: [siswa_id => [mapel_id => rekap]]
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

    // Rata-rata per mapel (footer row)
    #[Computed]
    public function rataPerMapel(): array
    {
        $rata = [];
        foreach ($this->mapelList as $mapel) {
            $nilais = [];
            foreach ($this->siswaList as $siswa) {
                $rekap = $this->rekapData[$siswa->id][$mapel->id] ?? null;
                if ($rekap && $rekap->nilai_akhir !== null) {
                    $nilais[] = $rekap->nilai_akhir;
                }
            }
            $rata[$mapel->id] = count($nilais) > 0 ? round(array_sum($nilais) / count($nilais), 1) : null;
        }
        return $rata;
    }

    public function hitungUlangSemua(): void
    {
        if (!$this->kelas_id || !$this->tahun_ajaran_id) {
            Notification::make()->title('Pilih kelas dan tahun ajaran terlebih dahulu')->warning()->send();
            return;
        }

        $siswaIds = $this->siswaList->pluck('id');
        $mapelIds = $this->mapelList->pluck('id');
        $count    = 0;

        foreach ($siswaIds as $siswaId) {
            foreach ($mapelIds as $mapelId) {
                PenilaianRekap::kalkulasiDanSimpan(
                    $siswaId,
                    $this->kelas_id,
                    $mapelId,
                    $this->tahun_ajaran_id,
                    (int) $this->semester
                );
                $count++;
            }
        }

        unset($this->rekapData, $this->rataPerMapel);

        Notification::make()
            ->title("Rekap berhasil dihitung ulang ({$count} kombinasi)")
            ->success()
            ->send();
    }

    public function exportExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $kelas       = Kelas::find($this->kelas_id);
        $tahunAjaran = TahunAjaran::find($this->tahun_ajaran_id);
        $fileName    = 'rekap_nilai_' . ($kelas?->nama_kelas ?? 'kelas') . '_semester_' . $this->semester . '.xlsx';

        return Excel::download(
            new \Modules\Penilaian\Exports\RekapNilaiExport(
                $this->kelas_id,
                $this->tahun_ajaran_id,
                $this->semester,
                $this->siswaList,
                $this->mapelList,
                $this->rekapData
            ),
            $fileName
        );
    }
}