<?php

namespace Database\Factories;

use App\Enums\JenisToner;
use App\Enums\StatusPermohonanToner;
use App\Models\PermohonanToner;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PermohonanToner>
 */
class PermohonanTonerFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        $year = now()->year;
        $seq = str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT);

        return [
            'no_tiket' => "TON-{$year}-{$seq}",
            'user_id' => User::factory(),
            'model_pencetak' => $this->faker->randomElement([
                'HP LaserJet Pro M404n',
                'Canon LBP6030',
                'Brother HL-L2350DW',
                'Epson M200',
            ]),
            'jenama_toner' => $this->faker->randomElement(['HP', 'Canon', 'Brother', 'Epson']),
            'jenis_toner' => $this->faker->randomElement(JenisToner::cases())->value,
            'no_siri_toner' => $this->faker->optional()->numerify('SN-########'),
            'kuantiti' => $this->faker->numberBetween(1, 10),
            'lokasi_pencetak' => 'Tingkat '.$this->faker->numberBetween(1, 10).', Bilik '.$this->faker->numberBetween(100, 999),
            'tujuan' => $this->faker->sentence(20),
            'tarikh_diperlukan' => $this->faker->optional()->dateTimeBetween('now', '+30 days'),
            'status' => StatusPermohonanToner::Submitted->value,
        ];
    }

    public function submitted(): static
    {
        return $this->state(['status' => StatusPermohonanToner::Submitted->value]);
    }

    public function disemak(): static
    {
        return $this->state(['status' => StatusPermohonanToner::Disemak->value]);
    }

    public function diluluskan(): static
    {
        return $this->state(['status' => StatusPermohonanToner::Diluluskan->value]);
    }

    public function ditolak(): static
    {
        return $this->state(['status' => StatusPermohonanToner::Ditolak->value]);
    }
}
