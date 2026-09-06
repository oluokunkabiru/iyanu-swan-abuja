<?php

namespace Database\Factories;

use App\Models\NewsPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<NewsPost>
 */
class NewsPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(20),
            'category' => fake()->randomElement(['Chapter', 'ICAN', 'Profession', 'Advocacy']),
            'author' => fake()->name(),
            'body' => fake()->paragraphs(4),
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'is_published' => true,
        ];
    }
}
