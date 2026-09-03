<?php

namespace Database\Factories;

use App\Models\Firm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Firm>
 */
class FirmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'principal_user_id' => null,
            'name' => fake()->company(),
            'principal' => fake()->name(),
            'licence_number' => fake()->unique()->bothify('PL/####/####'),
            'services' => fake()->randomElements(['Audit', 'Tax', 'Advisory', 'Forensic accounting'], 2),
            'area' => fake()->city(),
            'licence_status' => 'Active',
            'is_active' => true,
        ];
    }
}
