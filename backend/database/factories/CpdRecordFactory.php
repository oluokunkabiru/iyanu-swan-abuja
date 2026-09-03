<?php

namespace Database\Factories;

use App\Models\CpdRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CpdRecord>
 */
class CpdRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_id' => null,
            'activity' => fake()->sentence(5),
            'activity_date' => fake()->dateTimeBetween('-3 years', 'now'),
            'hours' => fake()->randomFloat(2, 1, 20),
            'activity_type' => fake()->randomElement(['Structured', 'Unstructured']),
            'is_verified' => false,
        ];
    }
}
