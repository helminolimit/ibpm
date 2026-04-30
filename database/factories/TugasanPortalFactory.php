<?php

namespace Database\Factories;

use App\Models\PermohonanPortal;
use App\Models\TugasanPortal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TugasanPortal>
 */
class TugasanPortalFactory extends Factory
{
    protected $model = TugasanPortal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'permohonan_portal_id' => PermohonanPortal::factory(),
            'teknisian_id' => User::factory(),
            'ditugaskan_oleh' => User::factory(),
            'nota_tugasan' => fake()->optional()->sentence(),
            'status_tugasan' => 'baharu',
        ];
    }

    public function dalamProses(): static
    {
        return $this->state(fn () => ['status_tugasan' => 'dalam_proses']);
    }

    public function selesai(): static
    {
        return $this->state(fn () => ['status_tugasan' => 'selesai']);
    }
}
