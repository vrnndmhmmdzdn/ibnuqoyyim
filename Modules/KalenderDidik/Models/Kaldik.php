<?php

namespace Modules\KalenderDidik\Models; 

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Modules\KalenderDidik\Database\Factories\KaldikFactory;

class Kaldik extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return KaldikFactory::new();
    }

    const KATEGORI = [
        'Akademik'     => 'Akademik',
        'Non-Akademik' => 'Non-Akademik',
        'Ujian'        => 'Ujian',
        'Libur'        => 'Libur',
    ];
    // Enum untuk lapangan jadwal
    const SUBJECTS = [
        'Kelas 1' => 'Kelas 1',
        'Kelas 2' => 'Kelas 2',
        'Kelas 3' => 'Kelas 3',
        'Kelas 4' => 'Kelas 4',
        'Kelas 5' => 'Kelas 5',
        'Kelas 6' => 'Kelas 6',
        'Semua Kelas' => 'Semua Kelas',
    ];

    protected $fillable = [
        'nama_acara',
        'kegiatan',
        'kategori',
        'subject',
        'tahun_ajaran',
        'jam_mulai',
        'jam_selesai',
        'notes',
    ];

    protected $casts = [
        'jam_mulai' => 'datetime',
        'jam_selesai' => 'datetime',
    ];

    /**
     * Scope untuk mendapatkan kaldik pada tanggal tertentu
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('jam_mulai', $date);
    }

    /**
     * Scope untuk mendapatkan kaldik dalam rentang tanggal
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('jam_mulai', [$startDate, $endDate]);
    }

    /**
     * Scope untuk mendapatkan kaldik berdasarkan lapangan
     */
    public function scopeByCourt($query, $court)
    {
        return $query->where('subject', $court);
    }

    /**
     * Accessor untuk mendapatkan durasi kaldik dalam jam
     */
    public function getDurationAttribute()
    {
        return $this->jam_mulai->diffInHours($this->jam_selesai);
    }

    /**
     * Accessor untuk format waktu yang mudah dibaca
     */
    public function getTimeRangeAttribute()
    {
        return $this->jam_mulai->format('H:i') . ' - ' . $this->jam_selesai->format('H:i');
    }

    /**
     * Accessor untuk mendapatkan total harga berdasarkan durasi
     */
    public function getTotalPriceAttribute()
    {
        return $this->price * $this->duration;
    }

    /**
     * Method untuk mengecek apakah kaldik bentrok dengan kaldik lain
     */
    public function isConflictWith($startAt, $endAt, $court)
    {
        return static::where('subject', $court)
            ->where(function ($query) use ($startAt, $endAt) {
                $query->whereBetween('jam_mulai', [$startAt, $endAt])
                    ->orWhereBetween('jam_selesai', [$startAt, $endAt])
                    ->orWhere(function ($q) use ($startAt, $endAt) {
                        $q->where('jam_mulai', '<=', $startAt)
                          ->where('jam_selesai', '>=', $endAt);
                    });
            })
            ->exists();
    }
}