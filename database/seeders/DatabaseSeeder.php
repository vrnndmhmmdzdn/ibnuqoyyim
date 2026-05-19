<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        User::factory()->create([
            'name' => 'Chika',
            'email' => 'chika@example.com',
        ]);
        $this->call([
            \Modules\Guru\Database\Seeders\GuruSeeder::class,
            \Modules\Angkatan\Database\Seeders\AngkatanSeeder::class,
            \Modules\TahunAjaran\Database\Seeders\TahunAjaranSeeder::class,
            \Modules\KalenderDidik\Database\Seeders\KaldikSeeder::class,
            \Modules\Kelas\Database\Seeders\KelasSeeder::class,
            \Modules\Siswa\Database\Seeders\SiswaSeeder::class,
            \Modules\MataPelajaran\Database\Seeders\MataPelajaranSeeder::class,
            // \Modules\JadwalPelajaran\Database\Seeders\JadwalPelajaranSeeder::class
            // Tambahkan class seeder modul lainnya di bawah ini...
        ]);
    }
}
