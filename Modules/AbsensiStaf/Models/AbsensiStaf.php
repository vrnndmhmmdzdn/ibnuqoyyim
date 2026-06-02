<?php

namespace Modules\AbsensiStaf\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Guru\Models\Guru;
use Carbon\Carbon;

class AbsensiStaf extends Model
{
    use HasFactory;

    protected $table = 'absensi_stafs';

    protected $fillable = [
        'guru_id',
        'tanggal',
        'clock_in_at',
        'clock_in_foto',
        'clock_in_lat',
        'clock_in_lng',
        'clock_out_at',
        'clock_out_foto',
        'clock_out_lat',
        'clock_out_lng',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'clock_in_at'  => 'datetime',
        'clock_out_at' => 'datetime',
    ];

    // Jam kerja
    const JAM_MASUK       = '07:30';
    const JAM_TOLERANSI   = '07:35'; // lewat ini = terlambat
    const JAM_PULANG      = '16:00';
    const JAM_PULANG_SABTU = '11:00';

    const STATUS = [
        'hadir'    => 'Hadir',
        'terlambat'=> 'Terlambat',
        'izin'     => 'Izin',
        'sakit'    => 'Sakit',
        'alpha'    => 'Alpha',
        'libur'    => 'Libur',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    // Sudah clock in atau belum
    public function getSudahClockInAttribute(): bool
    {
        return !is_null($this->clock_in_at);
    }

    // Sudah clock out atau belum
    public function getSudahClockOutAttribute(): bool
    {
        return !is_null($this->clock_out_at);
    }

    // Durasi kerja
    public function getDurasiAttribute(): ?string
    {
        if (!$this->clock_in_at || !$this->clock_out_at) return null;
        $menit = $this->clock_in_at->diffInMinutes($this->clock_out_at);
        $jam   = intdiv($menit, 60);
        $sisa  = $menit % 60;
        return "{$jam}j {$sisa}m";
    }

    // Keterlambatan dalam menit
    public function getTelatAttribute(): ?int
    {
        if (!$this->clock_in_at) return null;
        $batas = Carbon::parse($this->tanggal->format('Y-m-d') . ' ' . self::JAM_TOLERANSI);
        if ($this->clock_in_at->lte($batas)) return 0;
        return $this->clock_in_at->diffInMinutes($batas);
    }

    // Warna per status
    public static function warnaStatus(string $status): string
    {
        return match($status) {
            'hadir'     => 'success',
            'terlambat' => 'warning',
            'izin'      => 'info',
            'sakit'     => 'info',
            'alpha'     => 'danger',
            'libur'     => 'gray',
            default     => 'gray',
        };
    }

    // Tentukan status berdasarkan jam clock in
    public static function tentukanStatus(Carbon $clockIn, Carbon $tanggal): string
    {
        $batas = Carbon::parse($tanggal->format('Y-m-d') . ' ' . self::JAM_TOLERANSI);
        return $clockIn->lte($batas) ? 'hadir' : 'terlambat';
    }
}