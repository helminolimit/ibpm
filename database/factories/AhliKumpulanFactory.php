<?php

namespace Database\Factories;

use App\Enums\JenisTindakan;
use App\Models\AhliKumpulan;
use App\Models\PermohonanEmel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AhliKumpulan>
 */
class AhliKumpulanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'permohonan_id' => PermohonanEmel::factory(),
            'nama_ahli' => fake()->name(),
            'emel_ahli' => fake()->unique()->safeEmail(),
            'tindakan' => fake()->randomElement(JenisTindakan::cases()),
        ];
    }
}
