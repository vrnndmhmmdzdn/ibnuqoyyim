<?php

namespace Modules\Siswa\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Siswa\Models\Siswa;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        // Menggunakan withoutEvents() agar Filament tidak mencoba memproses mutasi file/foto saat seeding
        Siswa::withoutEvents(function () {
            // Generate 50 data siswa palsu
            Siswa::factory()->count(50)->create();
        });
    }
}
