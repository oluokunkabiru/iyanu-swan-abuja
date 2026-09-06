<?php

namespace Database\Factories;

use App\Models\Publication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Publication>
 */
class PublicationFactory extends Factory
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
            'category' => fake()->randomElement(['Communiqué', 'Newsletter', 'Technical', 'Report', 'Address']),
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
