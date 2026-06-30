<?php

namespace Modules\Penilaian\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Kelas\Models\Kelas;
use Modules\MataPelajaran\Models\MataPelajaran;
use Modules\Guru\Models\Guru;
use Modules\TahunAjaran\Models\TahunAjaran;
use Modules\Penilaian\Models\PenilaianItem;
use Modules\Penilaian\Models\PenilaianNilai;
use Modules\Penilaian\Models\PenilaianRekap;
use Modules\Penilaian\Models\PenilaianKonfigurasi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenilaianSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate dengan urutan yang benar (child dulu)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        PenilaianNilai::truncate();
        PenilaianRekap::truncate();
        PenilaianItem::truncate();
        PenilaianKonfigurasi::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAjaran) {
            $this->command->warn('Tidak ada tahun ajaran aktif. Jalankan TahunAjaranSeeder dulu.');
            return;
        }

        // Konfigurasi bobot default
        PenilaianKonfigurasi::create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'bobot_harian'    => 30,
            'bobot_tugas'     => 20,
            'bobot_pts'       => 20,
            'bobot_pas'       => 30,
        ]);

        $kelasList = Kelas::limit(3)->get();
        $mapelList = MataPelajaran::where('kategori', 'Umum')->limit(4)->get();
        $guru      = Guru::first();

        if ($kelasList->isEmpty() || $mapelList->isEmpty()) {
            $this->command->warn('Data kelas atau mata pelajaran kosong. Seed dibatalkan.');
            return;
        }

        foreach ($kelasList as $kelas) {
            // Ambil siswa di kelas via kelas_pivot
            $siswaIds = DB::table('kelas_pivot')
                ->where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $tahunAjaran->id)
                ->where('is_aktif', true)
                ->whereNull('deleted_at')
                ->pluck('siswa_id');

            if ($siswaIds->isEmpty()) continue;

            foreach ($mapelList as $mapel) {
                foreach (['1', '2'] as $semester) {
                    // Buat items
                    $items = [];

                    // 3 nilai harian
                    for ($i = 1; $i <= 3; $i++) {
                        $items[] = PenilaianItem::create([
                            'kelas_id'          => $kelas->id,
                            'mata_pelajaran_id'  => $mapel->id,
                            'guru_id'            => $guru?->id,
                            'tahun_ajaran_id'    => $tahunAjaran->id,
                            'semester'           => $semester,
                            'jenis'              => 'harian',
                            'judul'              => "Ulangan Harian {$i}",
                            'tanggal'            => Carbon::now()->subMonths($semester === '1' ? 4 : 1)->addDays($i * 7)->format('Y-m-d'),
                        ]);
                    }

                    // 2 tugas
                    for ($i = 1; $i <= 2; $i++) {
                        $items[] = PenilaianItem::create([
                            'kelas_id'          => $kelas->id,
                            'mata_pelajaran_id'  => $mapel->id,
                            'guru_id'            => $guru?->id,
                            'tahun_ajaran_id'    => $tahunAjaran->id,
                            'semester'           => $semester,
                            'jenis'              => 'tugas',
                            'judul'              => "Tugas {$i}",
                            'tanggal'            => Carbon::now()->subMonths($semester === '1' ? 3 : 0)->addDays($i * 10)->format('Y-m-d'),
                        ]);
                    }

                    // PTS
                    $items[] = PenilaianItem::create([
                        'kelas_id'          => $kelas->id,
                        'mata_pelajaran_id'  => $mapel->id,
                        'guru_id'            => $guru?->id,
                        'tahun_ajaran_id'    => $tahunAjaran->id,
                        'semester'           => $semester,
                        'jenis'              => 'pts',
                        'judul'              => "PTS Semester {$semester}",
                        'tanggal'            => Carbon::now()->subMonths($semester === '1' ? 2 : 0)->format('Y-m-d'),
                    ]);

                    // PAS
                    $items[] = PenilaianItem::create([
                        'kelas_id'          => $kelas->id,
                        'mata_pelajaran_id'  => $mapel->id,
                        'guru_id'            => $guru?->id,
                        'tahun_ajaran_id'    => $tahunAjaran->id,
                        'semester'           => $semester,
                        'jenis'              => 'pas',
                        'judul'              => "PAS Semester {$semester}",
                        'tanggal'            => Carbon::now()->subMonths($semester === '1' ? 1 : 0)->format('Y-m-d'),
                    ]);

                    // Isi nilai untuk setiap siswa
                    foreach ($siswaIds as $siswaId) {
                        foreach ($items as $item) {
                            PenilaianNilai::create([
                                'item_id'  => $item->id,
                                'siswa_id' => $siswaId,
                                'nilai'    => fake()->numberBetween(60, 100),
                                'catatan'  => null,
                            ]);
                        }

                        // Hitung rekap
                        PenilaianRekap::kalkulasiDanSimpan(
                            $siswaId,
                            $kelas->id,
                            $mapel->id,
                            $tahunAjaran->id,
                            (int) $semester
                        );
                    }
                }
            }
        }

        $this->command->info('PenilaianSeeder selesai.');
    }
}