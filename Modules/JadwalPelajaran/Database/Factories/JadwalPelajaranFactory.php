<?php

namespace Modules\JadwalPelajaran\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\JadwalPelajaran\Models\JadwalPelajaran;
use Modules\Kelas\Models\Kelas;
use Modules\MataPelajaran\Models\MataPelajaran;
use Modules\TahunAjaran\Models\TahunAjaran;

class JadwalPelajaranFactory extends Factory
{
    protected $model = JadwalPelajaran::class;

    public function definition(): array
    {
        $jamMulai  = fake()->randomElement(array_keys(JadwalPelajaran::JAM_SLOT));
        $jamSelesai = date('H:i', strtotime($jamMulai) + 90 * 60); // +90 menit

        return [
            'kelas_id'          => Kelas::inRandomOrder()->first()?->id,
            'mata_pelajaran_id' => MataPelajaran::inRandomOrder()->first()?->id,
            'guru_id'           => null, // nullable dulu
            'tahun_ajaran_id'   => TahunAjaran::where('is_aktif', true)->first()?->id,
            'hari'              => fake()->randomElement(array_keys(JadwalPelajaran::HARI)),
            'jam_mulai'         => $jamMulai,
            'jam_selesai'       => $jamSelesai,
        ];
    }
}