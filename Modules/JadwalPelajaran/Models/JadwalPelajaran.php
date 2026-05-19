<?php

namespace Modules\JadwalPelajaran\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\JadwalPelajaran\Database\Factories\JadwalPelajaranFactory;
use Modules\Guru\Models\Guru;
use Modules\Kelas\Models\Kelas;
use Modules\MataPelajaran\Models\MataPelajaran;
use Modules\TahunAjaran\Models\TahunAjaran;

class JadwalPelajaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jadwal_pelajarans';

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'guru_id',
        'tahun_ajaran_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    const HARI = [
        'Senin'  => 'Senin',
        'Selasa' => 'Selasa',
        'Rabu'   => 'Rabu',
        'Kamis'  => 'Kamis',
        'Jumat'  => 'Jumat',
        'Sabtu'  => 'Sabtu',
    ];

    // Slot waktu standar — dipakai di form jadwal
    const JAM_SLOT = [
        '07:30' => '07:30',
        '08:00' => '08:00',
        '08:30' => '08:30',
        '09:00' => '09:00',
        '09:30' => '09:30',
        '10:00' => '10:00',
        '10:30' => '10:30',
        '11:00' => '11:00',
        '11:30' => '11:30',
        '12:00' => '12:00',
        '13:00' => '13:00',
        '13:30' => '13:30',
        '14:00' => '14:00',
        '14:30' => '14:30',
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

    // Accessor — "07:00 - 08:30"
    public function getTimeRangeAttribute(): string
    {
        return $this->jam_mulai . ' - ' . $this->jam_selesai;
    }

    protected static function newFactory(): JadwalPelajaranFactory
    {
        return JadwalPelajaranFactory::new();
    }
}