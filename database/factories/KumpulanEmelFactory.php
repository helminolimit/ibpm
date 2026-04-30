<?php

namespace Database\Factories;

use App\Models\KumpulanEmel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KumpulanEmel>
 */
class KumpulanEmelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_kumpulan' => fake()->words(3, true),
            'alamat_emel' => fake()->unique()->safeEmail(),
            'pemilik_unit' => fake()->randomElement(['Unit Infrastruktur & Keselamatan ICT', 'Unit Aplikasi', 'Unit Rangkaian']),
            'jumlah_ahli' => fake()->numberBetween(0, 50),
        ];
    }
}
