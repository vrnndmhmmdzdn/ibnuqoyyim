<?php

namespace Modules\Guru\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Guru\Models\Guru;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Guru::factory(50)->create();
        
        // Tambahkan beberapa data manual untuk testing
        Guru::create([
            'name' => 'Ismail Fauzi',
            'telephone' => '081234567890',
            'email' => 'ismail@ibnuqoyyim.sch.id',
            'tanggal_masuk' => '2023-01-01',
        ]);
        
        Guru::create([
            'name' => 'Pamedar Pam',
            'telephone' => '081987654321',
            'email' => 'pamedar@ibnuqoyyim.sch.id',
            'tanggal_masuk' => '2023-01-01',
        ]);
        
        Guru::create([
            'name' => 'Yunus Wirawan',
            'telephone' => '081122334455',
            'email' => 'yunus@ibnuqoyyim.sch.id',
            'tanggal_masuk' => '2023-01-01',
        ]);
    }
}