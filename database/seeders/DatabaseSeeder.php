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

        // User::factory()->create([
        //     'name' => 'Azla',
        //     'email' => 'azla@example.com',
        //     'role' => 'admin',
        // ]);
        // User::factory()->create([
        //     'name' => 'Guru',
        //     'email' => 'guru@example.com',
        //     'role' => 'guru',
        // ]);
        // User::factory()->create([
        //     'name' => 'Ortu',
        //     'email' => 'ortu@example.com',
        //     'role' => 'ortu',
        // ]);

        $this->call([

            // \Modules\Guru\Database\Seeders\GuruSeeder::class,
            // \Modules\Angkatan\Database\Seeders\AngkatanSeeder::class,
            // \Modules\Kelas\Database\Seeders\KelasSeeder::class,
            // \Modules\Siswa\Database\Seeders\SiswaSeeder::class,
            // \Modules\KalenderDidik\Database\Seeders\KaldikSeeder::class,
            // \Modules\MutabaahTahfidz\Database\Seeders\MutabaahSurahSeeder::class,
            // \Modules\MutabaahTahfidz\Database\Seeders\MutabaahRecordSeeder::class,
            // \Modules\Midtrans\Database\Seeders\MidtransSeeder::class,
            // \Modules\MataPelajaran\Database\Seeders\MataPelajaranSeeder::class,
            
            // \Modules\TahunAjaran\Database\Seeders\TahunAjaranSeeder::class,
            // \Modules\JadwalPelajaran\Database\Seeders\JadwalPelajaranSeeder::class,
            
            \Modules\KelasPivot\Database\Seeders\KelasPivotSeeder::class,
            // \Modules\AbsensiStaf\Database\Seeders\AbsensiStafSeeder::class,

            // \Modules\Penilaian\Database\Seeders\PenilaianSeeder::class,
            // \Modules\JurnalGuru\Database\Seeders\JurnalGuruSeeder::class,

            // \Modules\JadwalPelajaran\Database\Seeders\JadwalPelajaranSeeder::class,
            // \Modules\Midtrans\Database\Seeders\MidtransSeeder::class,



            // Tambahkan class seeder modul lainnya di bawah ini...
        ]);
    }
}
