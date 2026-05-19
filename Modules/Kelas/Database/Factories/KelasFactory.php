<?php

namespace Modules\Kelas\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Kelas\Models\Kelas;

class KelasFactory extends Factory
{
    protected $model = Kelas::class;

    // Tambahkan counter statis untuk menjamin urutan angka selalu unik
    protected static int $order = 1;

    public function definition(): array
    {
        return [
        ];
    }
}
