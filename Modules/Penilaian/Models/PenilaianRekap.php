<?php

namespace Modules\Penilaian\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Kelas\Models\Kelas;
use Modules\MataPelajaran\Models\MataPelajaran;
use Modules\Siswa\Models\Siswa;
use Modules\TahunAjaran\Models\TahunAjaran;

class PenilaianRekap extends Model
{
    protected $table = 'penilaian_rekap';

    protected $fillable = [
        'kelas_id',
        'siswa_id',
        'mata_pelajaran_id',
        'tahun_ajaran_id',
        'semester',
        'rata_harian',
        'rata_tugas',
        'nilai_pts',
        'nilai_pas',
        'nilai_akhir',
        'predikat',
    ];

    protected $casts = [
        'rata_harian' => 'float',
        'rata_tugas'  => 'float',
        'nilai_pts'   => 'float',
        'nilai_pas'   => 'float',
        'nilai_akhir' => 'float',
    ];

    const PREDIKAT_COLOR = [
        'A' => 'bg-green-100 text-green-700',
        'B' => 'bg-blue-100 text-blue-700',
        'C' => 'bg-amber-100 text-amber-700',
        'D' => 'bg-red-100 text-red-700',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    // ── Static Helpers ────────────────────────────────────────────────────────

    public static function hitungPredikat(float $nilai): string
    {
        return match(true) {
            $nilai >= 90 => 'A',
            $nilai >= 75 => 'B',
            $nilai >= 60 => 'C',
            default      => 'D',
        };
    }

    public static function kalkulasiDanSimpan(
        int $siswaId,
        int $kelasId,
        int $mapelId,
        int $tahunAjaranId,
        int $semester
    ): self {
        // Ambil bobot dari konfigurasi (fallback ke default)
        $config = PenilaianKonfigurasi::getOrDefault($tahunAjaranId);

        // Ambil semua items untuk kombinasi ini
        $items = PenilaianItem::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('semester', (string) $semester)
            ->whereNull('deleted_at')
            ->get();

        // Hitung rata-rata per jenis — filter by siswa_id
        $hitungRata = function (string $jenis) use ($items, $siswaId): ?float {
            $itemIds = $items->where('jenis', $jenis)->pluck('id');
            if ($itemIds->isEmpty()) return null;

            $nilais = PenilaianNilai::whereIn('item_id', $itemIds)
                ->where('siswa_id', $siswaId)
                ->whereNotNull('nilai')
                ->pluck('nilai');

            return $nilais->isEmpty() ? null : round($nilais->avg(), 2);
        };

        $rataHarian = $hitungRata('harian');
        $rataTugas  = $hitungRata('tugas');

        // PTS dan PAS — ambil nilai dari item tunggal (biasanya satu per semester)
        $nilaiPts = null;
        $ptsItems = $items->where('jenis', 'pts');
        if ($ptsItems->count() > 0) {
            $entry = PenilaianNilai::whereIn('item_id', $ptsItems->pluck('id'))
                ->where('siswa_id', $siswaId)
                ->whereNotNull('nilai')
                ->first();
            $nilaiPts = $entry?->nilai;
        }

        $nilaiPas = null;
        $pasItems = $items->where('jenis', 'pas');
        if ($pasItems->count() > 0) {
            $entry = PenilaianNilai::whereIn('item_id', $pasItems->pluck('id'))
                ->where('siswa_id', $siswaId)
                ->whereNotNull('nilai')
                ->first();
            $nilaiPas = $entry?->nilai;
        }

        // Hitung nilai akhir dengan bobot
        $nilaiAkhir = null;
        $komponenAda = array_filter([
            $rataHarian !== null,
            $rataTugas  !== null,
            $nilaiPts   !== null,
            $nilaiPas   !== null,
        ]);

        if (!empty($komponenAda)) {
            $total  = 0;
            $bobot  = 0;

            if ($rataHarian !== null) { $total += $rataHarian * ($config->bobot_harian / 100); $bobot += $config->bobot_harian; }
            if ($rataTugas  !== null) { $total += $rataTugas  * ($config->bobot_tugas  / 100); $bobot += $config->bobot_tugas; }
            if ($nilaiPts   !== null) { $total += $nilaiPts   * ($config->bobot_pts    / 100); $bobot += $config->bobot_pts; }
            if ($nilaiPas   !== null) { $total += $nilaiPas   * ($config->bobot_pas    / 100); $bobot += $config->bobot_pas; }

            // Normalisasi jika tidak semua komponen ada
            $nilaiAkhir = $bobot > 0 ? round(($total / $bobot) * 100, 2) : null;
        }

        $predikat = $nilaiAkhir !== null ? self::hitungPredikat($nilaiAkhir) : null;

        return static::updateOrCreate(
            [
                'siswa_id'         => $siswaId,
                'mata_pelajaran_id'=> $mapelId,
                'tahun_ajaran_id'  => $tahunAjaranId,
                'semester'         => (string) $semester,
            ],
            [
                'kelas_id'    => $kelasId,
                'rata_harian' => $rataHarian,
                'rata_tugas'  => $rataTugas,
                'nilai_pts'   => $nilaiPts,
                'nilai_pas'   => $nilaiPas,
                'nilai_akhir' => $nilaiAkhir,
                'predikat'    => $predikat,
            ]
        );
    }
}