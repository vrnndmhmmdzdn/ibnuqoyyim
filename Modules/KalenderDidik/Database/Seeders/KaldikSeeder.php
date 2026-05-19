<?php

namespace Modules\KalenderDidik\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\KalenderDidik\Models\Kaldik;

class KaldikSeeder extends Seeder
{
    public function run(): void
    {
        
        Kaldik::truncate();

        // Data dummy random
        Kaldik::factory(15)->create();

        // Data manual yang pasti ada dan berwarna
        $data = [
            [
                'nama_acara'   => 'Ujian Tengah Semester Ganjil',
                'kegiatan'     => 'Ujian tulis semua mata pelajaran semester ganjil',
                'kategori'     => 'Ujian',
                'subject'      => 'Semua Kelas',
                'tahun_ajaran' => '2025/2026',
                'jam_mulai'     => '2025-10-06 07:00:00',
                'jam_selesai'       => '2025-10-10 12:00:00',
                'notes'        => 'Siswa wajib membawa kartu peserta ujian',
            ],
            [
                'nama_acara'   => 'Ujian Akhir Semester Ganjil',
                'kegiatan'     => 'Ujian tulis semua mata pelajaran akhir semester ganjil',
                'kategori'     => 'Ujian',
                'subject'      => 'Semua Kelas',
                'tahun_ajaran' => '2025/2026',
                'jam_mulai'     => '2025-12-08 07:00:00',
                'jam_selesai'       => '2025-12-13 12:00:00',
                'notes'        => null,
            ],
            [
                'nama_acara'   => 'Libur Semester Ganjil',
                'kegiatan'     => 'Hari libur resmi seluruh warga sekolah',
                'kategori'     => 'Libur',
                'subject'      => 'Semua Kelas',
                'tahun_ajaran' => '2025/2026',
                'jam_mulai'     => '2025-12-20 00:00:00',
                'jam_selesai'       => '2026-01-02 23:59:00',
                'notes'        => null,
            ],
            [
                'nama_acara'   => 'Penerimaan Rapor Semester Ganjil',
                'kegiatan'     => 'Pembagian rapor kepada orang tua wali murid',
                'kategori'     => 'Akademik',
                'subject'      => 'Semua Kelas',
                'tahun_ajaran' => '2025/2026',
                'jam_mulai'     => '2025-12-19 08:00:00',
                'jam_selesai'       => '2025-12-19 12:00:00',
                'notes'        => 'Orang tua wajib hadir',
            ],
            [
                'nama_acara'   => 'Pesantren Kilat',
                'kegiatan'     => 'Kegiatan keagamaan dan pembinaan akhlak siswa',
                'kategori'     => 'Non-Akademik',
                'subject'      => 'Semua Kelas',
                'tahun_ajaran' => '2025/2026',
                'jam_mulai'     => '2026-03-10 07:00:00',
                'jam_selesai'       => '2026-03-12 16:00:00',
                'notes'        => 'Siswa menginap di sekolah',
            ],
            [
                'nama_acara'   => 'Ujian Tengah Semester Genap',
                'kegiatan'     => 'Ujian tulis semua mata pelajaran semester genap',
                'kategori'     => 'Ujian',
                'subject'      => 'Semua Kelas',
                'tahun_ajaran' => '2025/2026',
                'jam_mulai'     => '2026-03-23 07:00:00',
                'jam_selesai'       => '2026-03-27 12:00:00',
                'notes'        => null,
            ],
            [
                'nama_acara'   => 'Libur Idul Fitri',
                'kegiatan'     => 'Libur bersama peringatan Hari Raya Idul Fitri',
                'kategori'     => 'Libur',
                'subject'      => 'Semua Kelas',
                'tahun_ajaran' => '2025/2026',
                'jam_mulai'     => '2026-03-28 00:00:00',
                'jam_selesai'       => '2026-04-07 23:59:00',
                'notes'        => null,
            ],
            [
                'nama_acara'   => 'Ujian Akhir Semester Genap',
                'kegiatan'     => 'Ujian tulis semua mata pelajaran akhir tahun',
                'kategori'     => 'Ujian',
                'subject'      => 'Semua Kelas',
                'tahun_ajaran' => '2025/2026',
                'jam_mulai'     => '2026-05-25 07:00:00',
                'jam_selesai'       => '2026-05-30 12:00:00',
                'notes'        => null,
            ],
            [
                'nama_acara'   => 'Penerimaan Rapor Semester Genap',
                'kegiatan'     => 'Pembagian rapor akhir tahun kepada orang tua',
                'kategori'     => 'Akademik',
                'subject'      => 'Semua Kelas',
                'tahun_ajaran' => '2025/2026',
                'jam_mulai'     => '2026-06-13 08:00:00',
                'jam_selesai'       => '2026-06-13 12:00:00',
                'notes'        => 'Orang tua wajib hadir',
            ],
            [
                'nama_acara'   => 'Libur Akhir Tahun Ajaran',
                'kegiatan'     => 'Libur panjang akhir tahun ajaran 2025/2026',
                'kategori'     => 'Libur',
                'subject'      => 'Semua Kelas',
                'tahun_ajaran' => '2025/2026',
                'jam_mulai'     => '2026-06-20 00:00:00',
                'jam_selesai'       => '2026-07-14 23:59:00',
                'notes'        => null,
            ],
        ];

        foreach ($data as $item) {
            Kaldik::create($item);
        }
    
    }
}