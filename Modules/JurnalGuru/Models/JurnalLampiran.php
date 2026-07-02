<?php

namespace Modules\JurnalGuru\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class JurnalLampiran extends Model
{
    use HasFactory;

    protected $table = 'jurnal_lampirans';

    protected $fillable = [
        'jurnal_guru_id',
        'nama_file',
        'path',
        'tipe',
        'ukuran',
    ];

    const TIPE = [
        'foto_kegiatan' => 'Foto Kegiatan',
        'rpp'           => 'RPP',
        'modul'         => 'Modul',
        'xlsx'          => 'Spreadsheet',
        'lainnya'       => 'Lainnya',
    ];

    // Ekstensi yang diizinkan
    const EKSTENSI_ALLOWED = [
        'jpg', 'jpeg', 'png', 'webp',           // gambar
        'pdf',                                    // PDF
        'doc', 'docx',                            // Word
        'xls', 'xlsx', 'xlsm',                   // Excel
        'ppt', 'pptx',                            // PowerPoint
    ];

    public function jurnalGuru(): BelongsTo
    {
        return $this->belongsTo(JurnalGuru::class);
    }

    // Apakah file ini gambar (bisa dipreview)
    public function getIsImageAttribute(): bool
    {
        return in_array(
            strtolower(pathinfo($this->nama_file, PATHINFO_EXTENSION)),
            ['jpg', 'jpeg', 'png', 'webp']
        );
    }

    // URL untuk akses file
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    // Ukuran file dalam format readable
    public function getUkuranReadableAttribute(): string
    {
        if (!$this->ukuran) return '-';
        $kb = $this->ukuran / 1024;
        if ($kb < 1024) return round($kb, 1) . ' KB';
        return round($kb / 1024, 1) . ' MB';
    }

    // Deteksi tipe dari ekstensi
    public static function deteksiTipe(string $ekstensi): string
    {
        return match(strtolower($ekstensi)) {
            'jpg', 'jpeg', 'png', 'webp' => 'foto_kegiatan',
            'pdf'                         => 'modul',
            'doc', 'docx'                => 'rpp',
            'xls', 'xlsx', 'xlsm'        => 'xlsx',
            default                       => 'lainnya',
        };
    }
}