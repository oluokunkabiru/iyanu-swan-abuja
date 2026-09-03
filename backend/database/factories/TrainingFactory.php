<?php

namespace Database\Factories;

use App\Models\Training;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Training>
 */
class TrainingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'provider' => fake()->randomElement(['SWAN Abuja', 'ICAN MPD', 'Faculty']),
            'delivery_mode' => fake()->randomElement(['Physical', 'Virtual', 'Hybrid']),
            'starts_on' => fake()->dateTimeBetween('now', '+6 months'),
            'cpd_hours' => fake()->numberBetween(1, 8),
            'fee' => fake()->numberBetween(15_000, 60_000),
            'member_fee' => fake()->numberBetween(10_000, 35_000),
            'seats_available' => fake()->numberBetween(0, 80),
            'is_active' => true,
        ];
    }
}
