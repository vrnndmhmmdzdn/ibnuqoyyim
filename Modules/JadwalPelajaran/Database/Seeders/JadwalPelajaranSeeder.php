<?php

namespace Modules\JadwalPelajaran\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\JadwalPelajaran\Models\JadwalPelajaran;
use Modules\Kelas\Models\Kelas;
use Modules\MataPelajaran\Models\MataPelajaran;
use Modules\TahunAjaran\Models\TahunAjaran;

class JadwalPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        JadwalPelajaran::truncate();

        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        $kelasList   = Kelas::all();
        $mapelList   = MataPelajaran::where('is_aktif', true)
                        ->where('kategori', '!=', 'Ekstrakurikuler')
                        ->get();

        // Validasi data master sebelum dijalankan
        if (!$tahunAjaran) {
            $this->command->error('Gagal! Tidak ada Tahun Ajaran yang aktif.');
            return;
        }

        if ($kelasList->isEmpty() || $mapelList->isEmpty()) {
            $this->command->error('Gagal! Data Kelas atau Mata Pelajaran masih kosong.');
            return;
        }

        $haris = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        $jamSlots = [
            ['mulai' => '07:00', 'selesai' => '08:30'],
            ['mulai' => '08:30', 'selesai' => '10:00'],
            ['mulai' => '10:15', 'selesai' => '11:45'],
            ['mulai' => '13:00', 'selesai' => '14:30'],
        ];

        // Buat daftar ID guru dari 1 sampai 50
        $guruIds = range(1, 50);

        foreach ($kelasList as $kelas) {
            $mapelAcak = $mapelList->shuffle();
            $mapelIndex = 0;

            foreach ($haris as $hari) {
                foreach ($jamSlots as $slot) {
                    $mapel = $mapelAcak[$mapelIndex % $mapelAcak->count()];
                    $mapelIndex++;

                    // Pastikan kombinasi jadwal kelas ini belum pernah dibuat sebelumnya
                    $sudahAdaJadwal = JadwalPelajaran::where('kelas_id', $kelas->id)
                        ->where('hari', $hari)
                        ->where('jam_mulai', $slot['mulai'])
                        ->where('tahun_ajaran_id', $tahunAjaran->id)
                        ->exists();

                    if ($sudahAdaJadwal) continue;

                    // --- LOGIKA ANTI BENTROK JADWAL GURU ---
                    $guruTerpilih = null;
                    
                    // Acak daftar guru agar pembagian jam mengajar merata dan bervariasi
                    $guruKandidat = $guruIds;
                    shuffle($guruKandidat);

                    foreach ($guruKandidat as $guruId) {
                        // Cek apakah guru ini sudah mengajar di kelas lain pada hari dan jam yang sama
                        $guruBentrok = JadwalPelajaran::where('guru_id', $guruId)
                            ->where('hari', $hari)
                            ->where('jam_mulai', $slot['mulai'])
                            ->where('tahun_ajaran_id', $tahunAjaran->id)
                            ->exists();

                        // Jika tidak bentrok, maka gunakan guru ini
                        if (!$guruBentrok) {
                            $guruTerpilih = $guruId;
                            break;
                        }
                    }

                    // Jika semua guru (1-50) ternyata bentrok pada slot itu, paksa ambil salah satu secara acak
                    if (is_null($guruTerpilih)) {
                        $guruTerpilih = $guruIds[array_rand($guruIds)];
                    }

                    JadwalPelajaran::create([
                        'kelas_id'          => $kelas->id,
                        'mata_pelajaran_id' => $mapel->id,
                        'guru_id'           => $guruTerpilih, // ID Guru 1-50 berhasil masuk
                        'tahun_ajaran_id'   => $tahunAjaran->id,
                        'hari'              => $hari,
                        'jam_mulai'         => $slot['mulai'],
                        'jam_selesai'       => $slot['selesai'],
                    ]);
                }
            }
        }

        $this->command->info('Sukses! Dummy data Jadwal Pelajaran beserta plotting Guru (1-50) berhasil dibuat.');
    }
}
