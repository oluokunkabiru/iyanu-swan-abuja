<?php

namespace Database\Factories;

use App\Models\Slider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Slider>
 */
class SliderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'badge' => fake()->words(3, true),
            'title' => fake()->sentence(7),
            'description' => fake()->paragraph(),
            'cta_label' => 'Learn more',
            'cta_link' => '/about',
            'secondary_cta_label' => null,
            'secondary_cta_link' => null,
            'image_alt' => fake()->sentence(5),
            'image_position' => 'center',
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
