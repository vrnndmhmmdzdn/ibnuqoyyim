<?php

namespace Modules\JurnalGuru\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Guru\Models\Guru;
use Modules\JurnalGuru\Database\Factories\JurnalGuruFactory;
use Modules\JurnalGuru\Models\JurnalLampiran;
use Modules\Kelas\Models\Kelas;
use Modules\MataPelajaran\Models\MataPelajaran;
use Modules\TahunAjaran\Models\TahunAjaran;

class JurnalGuru extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jurnal_gurus';

    protected $fillable = [
        'guru_id',
        'kelas_id',
        'mata_pelajaran_id',
        'tahun_ajaran_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'pertemuan_ke',
        'materi',
        'kompetensi_dasar',
        'deskripsi_kegiatan',
        'metode_pembelajaran',
        'media_pembelajaran',
        'jumlah_hadir',
        'jumlah_tidak_hadir',
        'capaian',
        'tindak_lanjut',
        'catatan',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'submitted_at' => 'datetime',
    ];

    const METODE = [
        'ceramah'    => 'Ceramah',
        'diskusi'    => 'Diskusi',
        'praktik'    => 'Praktik',
        'demonstrasi'=> 'Demonstrasi',
        'tanya_jawab'=> 'Tanya Jawab',
        'penugasan'  => 'Penugasan',
        'lainnya'    => 'Lainnya',
    ];

    const CAPAIAN = [
        'tercapai' => 'Tercapai',
        'sebagian' => 'Sebagian',
        'belum'    => 'Belum Tercapai',
    ];

    const STATUS = [
        'draft'     => 'Draft',
        'submitted' => 'Submitted',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    // Accessor — jumlah total siswa
    public function getTotalSiswaAttribute(): int
    {
        return $this->jumlah_hadir + $this->jumlah_tidak_hadir;
    }

    // Accessor — persentase kehadiran
    public function getPersentaseHadirAttribute(): string
    {
        if ($this->total_siswa === 0) return '0%';
        return round(($this->jumlah_hadir / $this->total_siswa) * 100) . '%';
    }

    // Warna capaian untuk kalender
    public static function warnaCapaian(string $capaian): array
    {
        return match($capaian) {
            'tercapai' => ['bg' => '#22c55e', 'border' => '#16a34a'],
            'sebagian'  => ['bg' => '#f59e0b', 'border' => '#d97706'],
            'belum'     => ['bg' => '#ef4444', 'border' => '#dc2626'],
            default     => ['bg' => '#6b7280', 'border' => '#4b5563'],
        };
    }

    public function lampirans(): HasMany
    {
        return $this->hasMany(JurnalLampiran::class, 'jurnal_guru_id');
    }

    protected static function newFactory(): JurnalGuruFactory
    {
        return JurnalGuruFactory::new();
    }
}