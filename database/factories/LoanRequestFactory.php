<?php

namespace Database\Factories;

use App\Enums\LoanStatus;
use App\Enums\RelationshipType;
use App\Models\LoanRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoanRequest>
 */
class LoanRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'applicant_id' => User::factory(),
            'on_behalf_of' => null,
            'status' => LoanStatus::MenungguSokongan->value,
        ];
    }

    public function onBehalf(): static
    {
        return $this->state(fn (array $attributes) => [
            'on_behalf_of' => [
                'name' => fake()->name(),
                'position' => fake()->jobTitle(),
                'phone' => '03-' . fake()->numerify('#### ####'),
                'unit' => fake()->words(2, true),
                'relationship' => RelationshipType::RakanSeunit->value,
            ],
        ]);
    }
}
