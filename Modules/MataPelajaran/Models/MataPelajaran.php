<?php

namespace Modules\MataPelajaran\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\MataPelajaran\Database\Factories\MataPelajaranFactory;

class MataPelajaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mata_pelajarans';

    protected $fillable = [
        'pelajaran',
        'kategori',
        'is_aktif'
    ];

    const KATEGORI = [
        'Umum',
        'Agama',
        'Ekstrakurikuler',
    ];

    protected static function newFactory()
    {
        return MataPelajaranFactory::new();
    }
}