<?php

namespace Modules\TahunAjaran\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\JadwalPelajaran\Models\JadwalPelajaran;
use Modules\MataPelajaran\Models\MataPelajaran;
use Modules\TahunAjaran\Database\Factories\TahunAjaranFactory;

class TahunAjaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tahun_ajarans';

    protected $fillable = [
        'tahun_ajaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_aktif'
    ];

    protected static function newFactory()
    {
        return TahunAjaranFactory::new();
    }
}