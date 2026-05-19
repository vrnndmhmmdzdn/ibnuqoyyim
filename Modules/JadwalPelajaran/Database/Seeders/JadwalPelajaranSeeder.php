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

        $haris = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        $jamSlots = [
            ['mulai' => '07:00', 'selesai' => '08:30'],
            ['mulai' => '08:30', 'selesai' => '10:00'],
            ['mulai' => '10:15', 'selesai' => '11:45'],
            ['mulai' => '13:00', 'selesai' => '14:30'],
        ];

        foreach ($kelasList as $kelas) {
            $mapelAcak = $mapelList->shuffle();
            $mapelIndex = 0;

            foreach ($haris as $hari) {
                foreach ($jamSlots as $slot) {
                    $mapel = $mapelAcak[$mapelIndex % $mapelAcak->count()];
                    $mapelIndex++;

                    // Skip kalau kombinasi sudah ada
                    $sudahAda = JadwalPelajaran::where('kelas_id', $kelas->id)
                        ->where('hari', $hari)
                        ->where('jam_mulai', $slot['mulai'])
                        ->where('tahun_ajaran_id', $tahunAjaran->id)
                        ->exists();

                    if ($sudahAda) continue;

                    JadwalPelajaran::create([
                        'kelas_id'          => $kelas->id,
                        'mata_pelajaran_id' => $mapel->id,
                        'guru_id'           => null,
                        'tahun_ajaran_id'   => $tahunAjaran->id,
                        'hari'              => $hari,
                        'jam_mulai'         => $slot['mulai'],
                        'jam_selesai'       => $slot['selesai'],
                    ]);
                }
            }
        }
    }
}