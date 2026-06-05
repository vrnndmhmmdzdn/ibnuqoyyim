<?php

namespace Modules\MutabaahTahfidz\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MutabaahRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataMutabaah = [];
        $jumlahHari = 15; // Histori 15 hari ke belakang
        
        $totalSiswa = 50;  // ID Siswa 1-50
        $totalKelas = 12;  // ID Kelas 1-12
        $totalSurah = 114; // ID Surah 1-114
        $totalGuru  = 53;  // ID Guru 1-53

        $this->command->info("Menghapus data mutabaah lama dan menyusun ulang data setoran padat...");
        
        // Bersihkan data lama agar aman dari duplikasi unique constraint
        DB::table('mutabaah_records')->truncate();

        // Variasi catatan ustadz/pembimbing tahfidz
        $catatanDummy = [
            'Hafalan lancar, tajwid bagus, pertahankan.',
            'Makhraj huruf sudah tepat, lanjutkan.',
            'Sedikit ragu di awal ayat, tingkatkan itqon.',
            'Kelancaran sangat baik, siap naik surah berikutnya.',
            'Alhamdulillah lancar dan tertib.',
            'Sangat baik, perhatikan panjang pendek harakat.',
            null
        ];

        for ($i = $jumlahHari; $i >= 0; $i--) {
            $tanggal = Carbon::today()->subDays($i);

            // Lewati hari Minggu (asumsi libur setoran)
            if ($tanggal->isSunday()) {
                continue;
            }

            for ($siswaId = 1; $siswaId <= $totalSiswa; $siswaId++) {
                
                // Menentukan kelas siswa secara konsisten (Siswa 1-50 terbagi rata ke kelas 1-12)
                $kelasId = ($siswaId % $totalKelas) + 1;

                // Tentukan guru pengampu acak
                $guruId = rand(1, $totalGuru);

                // --- MODIFIKASI PROBABILITAS (DIBUAT SANGAT RAJIN SETORAN) ---
                $randStatus = rand(1, 100);

                if ($randStatus <= 75) {
                    $status = 'lanjut';         // 75% Dominan melanjutkan hafalan baru
                } elseif ($randStatus <= 88) {
                    $status = 'ulang';          // 13% Mengulang (murojaah) hafalan lama
                } elseif ($randStatus <= 93) {
                    $status = 'tasmi';          // 5% Ujian tasmi sekali duduk
                } elseif ($randStatus <= 96) {
                    $status = 'membaca';        // 3% Hanya membaca/bin-nadzar
                } elseif ($randStatus <= 99) {
                    $status = 'tidak_setoran';  // 3% Sedikit sekali yang tidak siap setoran
                } else {
                    $status = 'tidak_masuk';    // Hanya 1% kemungkinan absen/sakit
                }

                // Inisialisasi default kolom Al-Qur'an
                $surahId    = null;
                $ayatAwal   = null;
                $ayatAkhir  = null;
                $jumlahAyat = 0;
                $nilai      = null;
                $catatan    = null;

                // Jika statusnya aktif melakukan aktivitas setoran (Total Probabilitas ~96%)
                if (in_array($status, ['lanjut', 'ulang', 'membaca', 'tasmi'])) {
                    $surahId   = rand(1, $totalSurah);
                    $ayatAwal  = rand(1, 10);
                    // Menentukan ayat akhir (berkisar antara 5 hingga 20 ayat setelah ayat awal)
                    $ayatAkhir = $ayatAwal + rand(5, 20); 
                    $jumlahAyat = ($ayatAkhir - $ayatAwal) + 1;

                    // Distribusi nilai performa setoran harian
                    $randNilai = rand(1, 100);
                    if ($randNilai <= 45) {
                        $nilai = 'mumtaz'; // 45% mayoritas santri mendapatkan predikat Mumtaz
                    } elseif ($randNilai <= 80) {
                        $nilai = 'jayyid_jiddan';
                    } elseif ($randNilai <= 97) {
                        $nilai = 'jayyid';
                    } else {
                        $nilai = 'rasib'; // Hanya 3% kemungkinan mendapat nilai kurang/mengulang
                    }

                    $catatan = $catatanDummy[array_rand($catatanDummy)];
                } elseif ($status === 'tidak_setoran') {
                    $catatan = 'Siswa hadir di halaqah namun belum siap menyetorkan hafalan.';
                } else {
                    $catatan = 'Siswa berhalangan hadir / izin.';
                }

                $dataMutabaah[] = [
                    'kelas_id'    => $kelasId,
                    'siswa_id'    => $siswaId,
                    'surah_id'    => $surahId,
                    'guru_id'     => $guruId,
                    'tanggal'     => $tanggal->format('Y-m-d'),
                    'status'      => $status,
                    'ayat_awal'   => $ayatAwal,
                    'ayat_akhir'  => $ayatAkhir,
                    'jumlah_ayat' => $jumlahAyat,
                    'nilai'       => $nilai,
                    'catatan'     => $catatan,
                    'created_at'  => $tanggal->copy()->hour(rand(7, 9))->minute(rand(0, 59)),
                    'updated_at'  => $tanggal->copy()->hour(rand(7, 9))->minute(rand(0, 59)),
                ];
            }

            // Eksekusi bulk insert per 200 data agar meringankan beban RAM PHP
            if (count($dataMutabaah) >= 200) {
                DB::table('mutabaah_records')->insert($dataMutabaah);
                $dataMutabaah = [];
            }
        }

        // Amankan sisa baris data yang belum ter-insert di loop akhir
        if (count($dataMutabaah) > 0) {
            DB::table('mutabaah_records')->insert($dataMutabaah);
        }

        $this->command->info("Selesai! Berhasil membuat dummy data setoran mutabaah padat harian untuk 50 siswa.");
    }
}
