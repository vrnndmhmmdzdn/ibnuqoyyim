<?php

namespace Modules\KelasPivot\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Kelas\Models\Kelas;
use Modules\KelasPivot\Database\Factories\KelasPivotFactory;
use Modules\Siswa\Models\Siswa;
use Modules\TahunAjaran\Models\TahunAjaran;

class KelasPivot extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kelas_pivot';

    protected $fillable = [
        'kelas_id',
        'siswa_id',
        'tahun_ajaran_id',
        'is_aktif',
        'deleted_at',
    ];

    protected static function newFactory()
    {
        // return KelasPivotFactory::new();
    }
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
}