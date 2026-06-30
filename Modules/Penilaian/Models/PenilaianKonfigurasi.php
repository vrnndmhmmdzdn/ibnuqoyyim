<?php

namespace Modules\Penilaian\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\TahunAjaran\Models\TahunAjaran;

class PenilaianKonfigurasi extends Model
{
    protected $table = 'penilaian_konfigurasi';

    protected $fillable = [
        'tahun_ajaran_id',
        'bobot_harian',
        'bobot_tugas',
        'bobot_pts',
        'bobot_pas',
    ];

    protected $casts = [
        'bobot_harian' => 'float',
        'bobot_tugas'  => 'float',
        'bobot_pts'    => 'float',
        'bobot_pas'    => 'float',
    ];

    // Default bobot jika belum dikonfigurasi
    const DEFAULT_BOBOT = [
        'harian' => 30,
        'tugas'  => 20,
        'pts'    => 20,
        'pas'    => 30,
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    // Ambil konfigurasi untuk tahun ajaran tertentu,
    // buat dengan default jika belum ada
    public static function getOrDefault(int $tahunAjaranId): self
    {
        return static::firstOrCreate(
            ['tahun_ajaran_id' => $tahunAjaranId],
            [
                'bobot_harian' => self::DEFAULT_BOBOT['harian'],
                'bobot_tugas'  => self::DEFAULT_BOBOT['tugas'],
                'bobot_pts'    => self::DEFAULT_BOBOT['pts'],
                'bobot_pas'    => self::DEFAULT_BOBOT['pas'],
            ]
        );
    }
}