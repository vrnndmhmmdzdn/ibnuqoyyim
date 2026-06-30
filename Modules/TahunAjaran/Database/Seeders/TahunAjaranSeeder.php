<?php

namespace Modules\TahunAjaran\Database\Seeders;

use Modules\TahunAjaran\Models\TahunAjaran; 
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // TahunAjaran::truncate();
        TahunAjaran::factory(5)->create();

        // TahunAjaran::create([
        //     'tahun_ajaran'    => '2025/2026',
        //     'tanggal_mulai'   => '2025-07-15',
        //     'tanggal_selesai' => '2026-06-30',
        //     'is_aktif'        => true,
        // ]);
    }
}