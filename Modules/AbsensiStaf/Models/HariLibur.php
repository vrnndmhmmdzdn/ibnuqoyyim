<?php

namespace Modules\AbsensiStaf\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    use HasFactory;

    protected $table = 'hari_liburs';

    protected $fillable = [
        'tanggal',
        'keterangan',
        'is_aktif',
    ];

    protected $casts = [
        'tanggal'  => 'date',
        'is_aktif' => 'boolean',
    ];

    // Cek apakah tanggal tertentu adalah hari libur
    public static function isLibur(\Carbon\Carbon $tanggal): bool
    {
        return static::where('tanggal', $tanggal->format('Y-m-d'))
            ->where('is_aktif', true)
            ->exists();
    }
}