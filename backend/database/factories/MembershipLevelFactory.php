<?php

namespace Database\Factories;

use App\Models\MembershipLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MembershipLevel>
 */
class MembershipLevelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Associate', 'Full Member', 'Fellow', 'Student']),
            'description' => fake()->sentence(),
            'subscription_amount' => 5_000,
            'welfare_amount' => 12_000,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
