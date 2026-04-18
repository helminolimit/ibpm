<?php

namespace Database\Factories;

use App\Models\KategoriAduan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KategoriAduan>
 */
class KategoriAduanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => fake()->randomElement([
                'Kerosakan Perkakasan',
                'Masalah Perisian',
                'Masalah Rangkaian',
                'Akaun & Akses',
                'E-mel & Komunikasi',
            ]),
            'unit_bpm' => fake()->randomElement([
                'Unit Infrastruktur',
                'Unit Aplikasi',
                'Unit Sokongan Pengguna',
            ]),
            'emel_unit' => fake()->safeEmail(),
            'is_aktif' => true,
        ];
    }

    public function tidakAktif(): static
    {
        return $this->state(['is_aktif' => false]);
    }
}
