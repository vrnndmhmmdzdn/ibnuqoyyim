<?php

namespace Modules\Kelas\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Kelas\Database\Factories\KelasFactory;
use Modules\Siswa\Models\Siswa;

class Kelas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
    ];

    protected static function newFactory()
    {
        return KelasFactory::new();
    }

    // public function siswas()
    // {
    //     return $this->belongsToMany(Siswa::class, 'kelas_pivot')
    //                 ->withPivot('is_aktif')
    //                 ->withTimestamps();
    // }
    public function siswas(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'kelas_pivot')  // ← ganti ini
            ->withPivot(['tahun_ajaran_id', 'is_aktif'])
            ->wherePivotNull('deleted_at')                         // ← tambah ini
            ->withTimestamps();
    }

    public function siswaAktif(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'kelas_pivot')  // ← ganti ini
            ->wherePivot('is_aktif', true)
            ->wherePivotNull('deleted_at')                         // ← tambah ini
            ->withPivot(['tahun_ajaran_id', 'is_aktif'])
            ->withTimestamps();
    }
}
