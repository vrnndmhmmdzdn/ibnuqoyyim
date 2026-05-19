<?php

namespace Modules\Angkatan\Database\Seeders;

use Modules\Angkatan\Models\Angkatan; 
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AngkatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Angkatan::factory(8)->create();
    }
}