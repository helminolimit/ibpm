<?php

namespace Database\Factories;

use App\Enums\JenisToner;
use App\Models\StokToner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StokToner>
 */
class StokTonerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'model_toner' => strtoupper($this->faker->bothify('??###?')),
            'jenama' => $this->faker->randomElement(['HP', 'Canon', 'Epson', 'Brother', 'Samsung']),
            'jenis_toner' => $this->faker->randomElement(JenisToner::cases()),
            'warna' => null,
            'kuantiti_ada' => $this->faker->numberBetween(0, 20),
            'kuantiti_minimum' => $this->faker->numberBetween(1, 5),
        ];
    }
}
