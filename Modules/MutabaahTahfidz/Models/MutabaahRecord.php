<?php

namespace Modules\MutabaahTahfidz\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Guru\Models\Guru;
use Modules\Kelas\Models\Kelas;
use Modules\Siswa\Models\Siswa;
use Modules\MutabaahTahfidz\Models\MutabaahSurah;

class MutabaahRecord extends Model
{
    use SoftDeletes;

    protected $table = 'mutabaah_records';

    protected $fillable = [
        'kelas_id',
        'siswa_id',
        'surah_id',
        'guru_id',
        'tanggal',
        'status',
        'ayat_awal',
        'ayat_akhir',
        'jumlah_ayat',
        'nilai',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ── Constants ───────────────────────────────────────────────────

    const STATUS = [
        'lanjut'         => 'Lanjut',
        'ulang'          => 'Ulang',
        'membaca'        => 'Membaca',
        'tasmi'          => 'Tasmi',
        'tidak_setoran'  => 'Tidak Setoran',
        'tidak_masuk'    => 'Tidak Masuk',
    ];

    const NILAI = [
        'rasib'         => 'Rasib',
        'jayyid'        => 'Jayyid',
        'jayyid_jiddan' => 'Jayyid Jiddan',
        'mumtaz'        => 'Mumtaz',
    ];

    // Statuses that require surah + ayat input
    const STATUS_NEEDS_SURAH = ['lanjut', 'ulang', 'membaca', 'tasmi'];

    // Statuses that require nilai input
    const STATUS_NEEDS_NILAI = ['lanjut', 'ulang', 'tasmi'];

    // ── Color helpers ────────────────────────────────────────────────

    public static function statusClass(string $status): string
    {
        return match ($status) {
            'lanjut'        => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
            'ulang'         => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
            'membaca'       => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            'tasmi'         => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
            'tidak_setoran' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
            'tidak_masuk'   => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
            default         => 'bg-gray-100 text-gray-600',
        };
    }

    public static function nilaiClass(string $nilai): string
    {
        return match ($nilai) {
            'rasib'         => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
            'jayyid'        => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
            'jayyid_jiddan' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            'mumtaz'        => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
            default         => 'bg-gray-100 text-gray-600',
        };
    }

    // Emoji helpers (used in WA copy)
    public static function statusEmoji(string $status): string
    {
        return match ($status) {
            'lanjut'        => '✅',
            'ulang'         => '🔁',
            'membaca'       => '📖',
            'tasmi'         => '🎓',
            'tidak_setoran' => '❌',
            'tidak_masuk'   => '⬜',
            default         => '❓',
        };
    }

    public static function nilaiEmoji(string $nilai): string
    {
        return match ($nilai) {
            'rasib'         => '😞',
            'jayyid'        => '🙂',
            'jayyid_jiddan' => '😊',
            'mumtaz'        => '⭐',
            default         => '',
        };
    }

    // ── Accessors ────────────────────────────────────────────────────

    public function getPositionLabelAttribute(): string
    {
        if (!$this->surah) return '-';
        return "{$this->surah->nama_surah} : {$this->ayat_awal}–{$this->ayat_akhir}";
    }

    // ── Relationships ────────────────────────────────────────────────

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function surah(): BelongsTo
    {
        return $this->belongsTo(MutabaahSurah::class, 'surah_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }
}