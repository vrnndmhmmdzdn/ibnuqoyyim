<?php

namespace Modules\JurnalGuru\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\JurnalGuru\Models\JurnalGuru;
use Modules\Guru\Models\Guru;
use Modules\Kelas\Models\Kelas;
use Modules\MataPelajaran\Models\MataPelajaran;
use Modules\TahunAjaran\Models\TahunAjaran;
use Carbon\Carbon;

class JurnalGuruSeeder extends Seeder
{
    public function run(): void
    {
        // JurnalGuru::truncate();

        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        $gurus       = Guru::all();
        $kelasList   = Kelas::all();
        $mapelList   = MataPelajaran::where('is_aktif', true)->get();

        // Generate jurnal 30 hari terakhir, skip weekend
        for ($hari = 29; $hari >= 0; $hari--) {
            $tanggal   = Carbon::now()->subDays($hari);
            $dayOfWeek = $tanggal->dayOfWeek;

            // Skip Minggu
            if ($dayOfWeek === 0) continue;

            foreach ($gurus as $guru) {
                // Setiap guru isi 1-2 jurnal per hari
                $jumlahJurnal = rand(1, 2);

                $usedKelas = [];
                $usedMapel = [];

                for ($j = 0; $j < $jumlahJurnal; $j++) {
                    $kelas = $kelasList->random();
                    $mapel = $mapelList->random();

                    // Skip kalau kombinasi sudah ada
                    $key = $kelas->id . '_' . $mapel->id;
                    if (in_array($key, array_map(
                        fn($k, $m) => $k . '_' . $m,
                        $usedKelas, $usedMapel
                    ))) continue;

                    $usedKelas[] = $kelas->id;
                    $usedMapel[] = $mapel->id;

                    // Cek apakah sudah ada
                    $sudahAda = JurnalGuru::where('guru_id', $guru->id)
                        ->where('kelas_id', $kelas->id)
                        ->where('mata_pelajaran_id', $mapel->id)
                        ->whereDate('tanggal', $tanggal->format('Y-m-d'))
                        ->exists();

                    if ($sudahAda) continue;

                    $jamMulai   = ['07:30', '08:30', '10:00', '13:00'][rand(0, 3)];
                    $jamSelesai = date('H:i', strtotime($jamMulai) + 90 * 60);

                    JurnalGuru::create([
                        'guru_id'            => $guru->id,
                        'kelas_id'           => $kelas->id,
                        'mata_pelajaran_id'  => $mapel->id,
                        'tahun_ajaran_id'    => $tahunAjaran->id,
                        'tanggal'            => $tanggal->format('Y-m-d'),
                        'jam_mulai'          => $jamMulai,
                        'jam_selesai'        => $jamSelesai,
                        'pertemuan_ke'       => rand(1, 20),
                        'materi'             => 'Materi pertemuan ' . rand(1, 10),
                        'kompetensi_dasar'   => '3.' . rand(1, 8) . ' Memahami konsep dasar',
                        'deskripsi_kegiatan' => 'Pembukaan, penyampaian materi, latihan, dan penutup.',
                        'metode_pembelajaran'=> ['ceramah', 'diskusi', 'praktik', 'tanya_jawab'][rand(0, 3)],
                        'media_pembelajaran' => ['Papan Tulis', 'Proyektor', 'Buku Paket', null][rand(0, 3)],
                        'jumlah_hadir'       => rand(20, 30),
                        'jumlah_tidak_hadir' => rand(0, 5),
                        'capaian'            => ['tercapai', 'tercapai', 'sebagian', 'belum'][rand(0, 3)],
                        'tindak_lanjut'      => rand(0, 1) ? 'Remedial dan pengayaan materi.' : null,
                        'catatan'            => rand(0, 1) ? 'Siswa aktif dalam pembelajaran.' : null,
                        'status'             => $hari > 3 ? 'submitted' : 'draft',
                        'submitted_at'       => $hari > 3 ? $tanggal->copy()->addHours(2) : null,
                    ]);
                }
            }
        }
    }
}