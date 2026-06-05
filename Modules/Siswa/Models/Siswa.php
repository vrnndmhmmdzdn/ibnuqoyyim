<?php
namespace Modules\Siswa\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Angkatan\Models\Angkatan;
use Modules\Kelas\Models\Kelas;
use Modules\Siswa\Database\Factories\SiswaFactory;

class Siswa extends Model
{
    use HasFactory, SoftDeletes;

    // Daftarkan kolom yang boleh diisi mass-assignment
    protected $fillable = [
        'nisn', 'nis', 'angkatan_id', 'status_siswa', 'tanggal_masuk',
        'nama_lengkap', 'nama_panggilan', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
        'agama', 'nik', 'email', 'nomor_hp', 'alamat', 'latitude', 'longitude',
        'nama_ayah', 'pekerjaan_ayah', 'nama_ibu', 'pekerjaan_ibu', 'nama_wali',
        'nomor_hp_orang_tua', 'foto_siswa', 'catatan_medis'
    ];

    // Cast tipe data agar otomatis menjadi object Carbon (Tanggal)
    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_lahir' => 'date',
    ];

    /**
     * Relasi: Siswa termasuk ke dalam satu Angkatan
     */
    public function angkatan(): BelongsTo
    {
        return $this->belongsTo(Angkatan::class);
    }
    protected static function newFactory()
    {
        return SiswaFactory::new();
    }
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_pivot')
                    ->withPivot('is_aktif')
                    ->withTimestamps();
    }
}
