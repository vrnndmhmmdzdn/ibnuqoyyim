<?php

namespace Modules\KalenderDidik\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\KalenderDidik\Models\Kaldik;

use Carbon\Carbon;

class KaldikFactory extends Factory
{
    protected $model = Kaldik::class;

    public function definition(): array
    {
        static $tahun = null;
        if ($tahun === null) {
            $tahun = now()->year;
        }

        $start = Carbon::create($tahun, fake()->numberBetween(7, 12), fake()->numberBetween(1, 28))
            ->setHour(fake()->randomElement([7, 8, 9, 10, 13]))
            ->setMinute(0)
            ->setSecond(0);

        $end = $start->copy()->addDays(fake()->numberBetween(0, 3))
            ->setHour(fake()->randomElement([10, 12, 14, 15, 16]));

        $kategori = fake()->randomElement(array_keys(Kaldik::KATEGORI));

        $namaAcara = match($kategori) {
            'Ujian'        => fake()->randomElement([
                'Ujian Tengah Semester', 'Ujian Akhir Semester',
                'Ujian Harian', 'Ujian Praktik',
            ]),
            'Libur'        => fake()->randomElement([
                'Libur Semester', 'Libur Nasional',
                'Libur Idul Fitri', 'Libur Akhir Tahun',
            ]),
            'Akademik'     => fake()->randomElement([
                'Penerimaan Rapor', 'Hari Guru Nasional',
                'Upacara Bendera', 'Pekan Olahraga Sekolah',
            ]),
            'Non-Akademik' => fake()->randomElement([
                'Kegiatan Pramuka', 'Pesantren Kilat',
                'Pentas Seni', 'Kunjungan Edukatif',
            ]),
            default => 'Kegiatan Sekolah',
        };

        $kegiatan = match($kategori) {
            'Ujian'        => fake()->randomElement([
                'Ujian tulis semua mata pelajaran',
                'Ujian lisan Bahasa Arab dan Bahasa Inggris',
                'Ujian praktik komputer dan kesenian',
            ]),
            'Libur'        => fake()->randomElement([
                'Hari libur resmi seluruh warga sekolah',
                'Libur bersama peringatan hari nasional',
            ]),
            'Akademik'     => fake()->randomElement([
                'Pembagian rapor semester ganjil kepada orang tua',
                'Upacara bendera rutin setiap Senin',
                'Kegiatan olahraga antar kelas',
            ]),
            'Non-Akademik' => fake()->randomElement([
                'Latihan pramuka dan kegiatan alam bebas',
                'Pentas seni dan bazar kreativitas siswa',
                'Kunjungan ke museum dan tempat bersejarah',
            ]),
            default => 'Kegiatan umum sekolah',
        };

        return [
            'nama_acara'   => $namaAcara,
            'kegiatan'     => $kegiatan,
            'kategori'     => $kategori,
            'subject'      => fake()->randomElement(array_keys(Kaldik::SUBJECTS)),
            'tahun_ajaran' => $tahun . '/' . ($tahun + 1),
            'jam_mulai'     => $start,
            'jam_selesai'       => $end,
            'notes'        => fake()->optional(0.4)->sentence(),
        ];
    }
}