<?php

namespace Database\Factories;

use App\Models\GalleryImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GalleryImage>
 */
class GalleryImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'caption' => fake()->sentence(4),
            'album' => fake()->randomElement(['Seminars', 'Community outreach', 'Chapter life']),
            'year' => fake()->numberBetween(2024, 2026),
            'event_id' => null,
        ];
    }
}
