<?php

namespace Modules\MutabaahTahfidz\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MutabaahSurah extends Model
{
    protected $table = 'mutabaah_surahs';

    protected $fillable = [
        'no_surah',
        'nama_surah',
        'jumlah_ayat',
        'juz',
    ];

    public function records(): HasMany
    {
        return $this->hasMany(MutabaahRecord::class, 'surah_id');
    }

    // "Q.S. Al Fatihah (1)" format
    public function getLabelAttribute(): string
    {
        return "Q.S. {$this->nama_surah} ({$this->no_surah})";
    }
}