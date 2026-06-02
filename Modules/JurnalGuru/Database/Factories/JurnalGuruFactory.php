<?php

namespace Modules\JurnalGuru\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\JurnalGuru\Models\JurnalGuru;
use Modules\Guru\Models\Guru;
use Modules\Kelas\Models\Kelas;
use Modules\MataPelajaran\Models\MataPelajaran;
use Modules\TahunAjaran\Models\TahunAjaran;
use Carbon\Carbon;

class JurnalGuruFactory extends Factory
{
    protected $model = JurnalGuru::class;

    public function definition(): array
    {
        $tanggal   = fake()->dateTimeBetween('-2 months', 'now');
        $jamMulai  = fake()->randomElement(['07:30', '08:00', '08:30', '09:00', '10:00', '13:00']);
        $jamSelesai = date('H:i', strtotime($jamMulai) + 90 * 60);

        return [
            'guru_id'            => Guru::inRandomOrder()->first()?->id,
            'kelas_id'           => Kelas::inRandomOrder()->first()?->id,
            'mata_pelajaran_id'  => MataPelajaran::inRandomOrder()->first()?->id,
            'tahun_ajaran_id'    => TahunAjaran::where('is_aktif', true)->first()?->id,
            'tanggal'            => $tanggal,
            'jam_mulai'          => $jamMulai,
            'jam_selesai'        => $jamSelesai,
            'pertemuan_ke'       => fake()->numberBetween(1, 20),
            'materi'             => fake()->randomElement([
                'Pengenalan Bilangan Cacah',
                'Penjumlahan dan Pengurangan',
                'Huruf Hijaiyah',
                'Tajwid Dasar',
                'Tata Cara Sholat',
                'Makna Surat Al-Fatihah',
                'Ekosistem dan Lingkungan',
                'Keberagaman Budaya Indonesia',
            ]),
            'kompetensi_dasar'   => '3.' . fake()->numberBetween(1, 8) . ' Memahami konsep dasar materi pembelajaran',
            'deskripsi_kegiatan' => fake()->randomElement([
                'Pembukaan dengan doa, penjelasan materi, latihan soal, dan penutup.',
                'Diskusi kelompok, presentasi hasil, dan evaluasi bersama.',
                'Praktik langsung, demonstrasi guru, dan tanya jawab.',
                'Review materi sebelumnya, penjelasan materi baru, dan tugas mandiri.',
            ]),
            'metode_pembelajaran'=> fake()->randomElement(array_keys(JurnalGuru::METODE)),
            'media_pembelajaran' => fake()->randomElement([
                'Papan Tulis, Spidol',
                'Proyektor, Slide PPT',
                'Buku Paket, LKS',
                'Video Pembelajaran',
                null,
            ]),
            'jumlah_hadir'       => fake()->numberBetween(20, 30),
            'jumlah_tidak_hadir' => fake()->numberBetween(0, 5),
            'capaian'            => fake()->randomElement(array_keys(JurnalGuru::CAPAIAN)),
            'tindak_lanjut'      => fake()->optional(0.7)->randomElement([
                'Remedial untuk siswa yang belum memahami materi.',
                'Pengayaan materi di pertemuan berikutnya.',
                'Memberikan tugas tambahan.',
            ]),
            'catatan'            => fake()->optional(0.4)->sentence(),
            'status'             => fake()->randomElement(['draft', 'submitted']),
            'submitted_at'       => null,
        ];
    }

    public function submitted(): static
    {
        return $this->state([
            'status'       => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function draft(): static
    {
        return $this->state([
            'status'       => 'draft',
            'submitted_at' => null,
        ]);
    }
}