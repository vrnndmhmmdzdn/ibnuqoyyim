<?php

namespace Modules\TahunAjaran\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\TahunAjaran\Models\TahunAjaran;
use Carbon\Carbon;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TahunAjaran>
 */
class TahunAjaranFactory extends Factory
{
    protected $model = TahunAjaran::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $tahun = null;
        if ($tahun === null) {
            $tahun = now()->year - 4; // mulai 4 tahun yang lalu
        }

        $current = $tahun;
        $tahun++;

        return [
            'tahun_ajaran'    => $current . '/' . ($current + 1),
            'tanggal_mulai'   => Carbon::create($current, 7, 15),
            'tanggal_selesai' => Carbon::create($current + 1, 6, 30),
            'is_aktif'        => false,
        ];
    }
    public function aktif(): static
    {
        $tahun = now()->year;

        return $this->state([
            'tahun_ajaran'    => $tahun . '/' . ($tahun + 1),
            'tanggal_mulai'   => Carbon::create($tahun, 7, 15),
            'tanggal_selesai' => Carbon::create($tahun + 1, 6, 30),
            'is_aktif'        => true,
        ]);
    }
}