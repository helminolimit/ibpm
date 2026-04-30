<?php

namespace Database\Factories;

use App\Enums\JenisTindakan;
use App\Enums\StatusPermohonanEmel;
use App\Models\KumpulanEmel;
use App\Models\PermohonanEmel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PermohonanEmel>
 */
class PermohonanEmelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'no_tiket' => 'GRP-'.now()->year.'-'.str_pad(fake()->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'user_id' => User::factory(),
            'pentadbir_id' => null,
            'kumpulan_emel_id' => KumpulanEmel::factory(),
            'jenis_tindakan' => fake()->randomElement(JenisTindakan::cases()),
            'status' => StatusPermohonanEmel::Baru,
            'catatan_pemohon' => fake()->optional()->sentence(),
            'catatan_pentadbir' => null,
            'selesai_at' => null,
        ];
    }
}
