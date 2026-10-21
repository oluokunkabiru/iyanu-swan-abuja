<?php

namespace Database\Factories;

use App\Models\Committee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Committee>
 */
class CommitteeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'purpose' => fake()->paragraph(),
            'chair' => fake()->name(),
            'objectives' => fake()->sentences(4),
            'structure' => fake()->paragraph(),
            'meetings' => fake()->sentence(),
            'reporting' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
