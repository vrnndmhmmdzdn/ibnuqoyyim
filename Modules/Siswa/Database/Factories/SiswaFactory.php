<?php

namespace Modules\Siswa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Siswa\Models\Siswa;

class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    public function definition(): array
    {
        return [
            // --- DATA AKADEMIK UTAMA ---
            'nisn' => $this->faker->unique()->numerify('##########'), // 10 digit string angka
            'nis' => $this->faker->unique()->numerify('2024####'),    // Format NIS diawali tahun masuk
            'angkatan_id' => 1, // Default ke ID angkatan pertama (pastikan data angkatan di-seed duluan)
            'status_siswa' => $this->faker->randomElement(['aktif', 'lulus', 'pindah', 'drop-out']),
            'tanggal_masuk' => $this->faker->date('Y-m-d', '-2 years'),

            // --- DATA PRIBADI ---
            'nama_lengkap' => $this->faker->name(),
            'nama_panggilan' => $this->faker->firstName(),
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'tempat_lahir' => $this->faker->city(),
            'tanggal_lahir' => $this->faker->date('Y-m-d', '-15 years'),
            'nik' => $this->faker->unique()->numerify('################'), // AMAN: 16 digit murni string

            // --- KONTAK & DOMISILI ---
            'email' => $this->faker->unique()->safeEmail(),
            'nomor_hp' => $this->faker->numerify('08##########'),
            'alamat' => $this->faker->address(),
            'latitude' => $this->faker->latitude(-7.9, -6.0),   // Area koordinat pulau jawa
            'longitude' => $this->faker->longitude(106.5, 108.5),

            // --- DATA ORANG TUA / WALI ---
            'nama_ayah' => $this->faker->name('male'),
            'pekerjaan_ayah' => $this->faker->jobTitle(),
            'nama_ibu' => $this->faker->name('female'),
            'pekerjaan_ibu' => $this->faker->jobTitle(),
            'nama_wali' => null,
            'nomor_hp_orang_tua' => $this->faker->numerify('08##########'),

            // --- ATRIBUT TAMBAHAN ---
            'foto_siswa' => null, // AMAN: diset null agar tidak memicu eror stream/file copy
            'catatan_medis' => $this->faker->randomElement(['Tidak ada', 'Alergi seafood', 'Asma ringan', null]),
        ];
    }
}
