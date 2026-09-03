<?php

namespace Database\Factories;

use App\Models\ResourceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResourceItem>
 */
class ResourceItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(5),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['Form', 'Guide', 'Policy', 'Template', 'Syllabus']),
            'format' => fake()->randomElement(['PDF', 'DOCX', 'XLSX']),
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }
}
