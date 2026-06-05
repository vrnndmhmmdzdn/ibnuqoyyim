<?php

namespace Modules\KelasPivot\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Kelas\Models\Kelas;
use Modules\KelasPivot\Models\KelasPivot; 
use Modules\Siswa\Models\Siswa;

class KelasPivotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $siswaIds = Siswa::pluck('id')->toArray(); 
        $kelasIds = Kelas::pluck('id')->toArray();
        $tahunAjaranId = 1;

        if(count($siswaIds) === 0 || count($kelasIds) === 0){
            $this->command->error('Tidak ada data siswa atau kelas untuk dihubungkan. Pastikan tabel siswa dan kelas sudah diisi.');   
            return;
        }

        shuffle($siswaIds);

        $pivotData = [];
        $totalSiswa = count($siswaIds);
        $totalKelas = count($kelasIds);

        foreach ($siswaIds as $index => $siswaId) {
            // Menentukan indeks kelas (0 sampai 11) menggunakan modulo %
            $kelasIndex = $index % $totalKelas;
            $kelasId = $kelasIds[$kelasIndex];

            $pivotData[] = [
                'kelas_id'        => $kelasId,
                'siswa_id'        => $siswaId,
                'tahun_ajaran_id' => $tahunAjaranId,
                'is_aktif'        => true, // Menyinkronkan dengan is_aktif di kode Blade Anda sebelumnya
                'created_at'      => now(),
                'updated_at'      => now(),
            ];
        }

        DB::table('kelas_pivot')->truncate();
        DB::table('kelas_pivot')->insert($pivotData);

    }
}