<?php

namespace Modules\Penilaian\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Guru\Models\Guru;
use Modules\Kelas\Models\Kelas;
use Modules\MataPelajaran\Models\MataPelajaran;
use Modules\TahunAjaran\Models\TahunAjaran;

class PenilaianItem extends Model
{
    use SoftDeletes;

    protected $table = 'penilaian_items';

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'guru_id',
        'tahun_ajaran_id',
        'semester',
        'jenis',
        'judul',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    const JENIS = [
        'harian' => 'Nilai Harian',
        'tugas'  => 'Tugas',
        'pts'    => 'PTS',
        'pas'    => 'PAS',
    ];

    const JENIS_LABEL_SHORT = [
        'harian' => 'NH',
        'tugas'  => 'NT',
        'pts'    => 'PTS',
        'pas'    => 'PAS',
    ];

    const WARNA_JENIS = [
        'harian' => 'bg-blue-100 text-blue-700',
        'tugas'  => 'bg-purple-100 text-purple-700',
        'pts'    => 'bg-amber-100 text-amber-700',
        'pas'    => 'bg-red-100 text-red-700',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function nilaiEntries(): HasMany
    {
        return $this->hasMany(PenilaianNilai::class, 'item_id');
    }
}