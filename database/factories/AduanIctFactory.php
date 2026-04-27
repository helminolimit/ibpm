<?php

namespace Database\Factories;

use App\Enums\StatusAduan;
use App\Models\AduanIct;
use App\Models\KategoriAduan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AduanIct>
 */
class AduanIctFactory extends Factory
{
    public function definition(): array
    {
        static $counter = 0;
        $counter++;
        $year = now()->year;

        return [
            'no_tiket' => 'ICT-'.$year.'-'.str_pad($counter, 3, '0', STR_PAD_LEFT),
            'user_id' => User::factory(),
            'kategori_aduan_id' => KategoriAduan::factory(),
            'lokasi' => 'Bilik '.fake()->numberBetween(100, 500).', Aras '.fake()->numberBetween(1, 10),
            'tajuk' => fake()->sentence(6),
            'keterangan' => fake()->paragraph(),
            'no_telefon' => '03-'.fake()->numerify('########'),
            'status' => StatusAduan::Baru,
        ];
    }

    public function dalamProses(): static
    {
        return $this->state(['status' => StatusAduan::DalamProses]);
    }

    public function selesai(): static
    {
        return $this->state(['status' => StatusAduan::Selesai, 'tarikh_selesai' => now()]);
    }
}
