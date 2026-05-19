<?php

namespace Modules\Angkatan\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Angkatan\Models\Angkatan;

class AngkatanFactory extends Factory
{
    protected $model = Angkatan::class;

    // Tambahkan counter statis untuk menjamin urutan angka selalu unik
    protected static int $order = 1;

    public function definition(): array
    {
        $currentOrder = static::$order++;
        
        return [
            "nama_angkatan" => "Angkatan " . $currentOrder,
            "angkatan_ke" => $currentOrder,
            "tahun_mulai" => 2018 + $currentOrder,
        ];
    }
}
