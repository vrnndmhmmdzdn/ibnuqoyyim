<?php

namespace Modules\Kelas\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Kelas\Models\Kelas;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        // Perulangan untuk tingkatan kelas (1 sampai 6)
        for ($tingkat = 1; $tingkat <= 6; $tingkat++) {
            
            // Perulangan untuk abjad kelas (A dan B)
            foreach (['A', 'B'] as $abjad) {
                
                $namaKelas = "Kelas " . $tingkat . $abjad;
                
                // updateOrCreate digunakan agar tidak duplikat jika seeder dirun ulang
                Kelas::updateOrCreate(
                    ['nama_kelas' => $namaKelas], // Kondisi cek data unik
                    ['nama_kelas' => $namaKelas]  // Data yang dimasukkan
                );
            }
        }
    }
}
