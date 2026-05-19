<?php

namespace Modules\Guru\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Guru\Models\Guru;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guru>
 */
class GuruFactory extends Factory
{
    protected $model = Guru::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'telephone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'tanggal_masuk' => fake()->date(),
        ];
    }
}