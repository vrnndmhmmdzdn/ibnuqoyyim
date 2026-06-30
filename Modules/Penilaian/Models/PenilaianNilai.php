<?php

namespace Modules\Penilaian\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Siswa\Models\Siswa;

class PenilaianNilai extends Model
{
    protected $table = 'penilaian_nilai';

    protected $fillable = [
        'item_id',
        'siswa_id',
        'nilai',
        'catatan',
    ];

    protected $casts = [
        'nilai' => 'float',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(PenilaianItem::class, 'item_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}