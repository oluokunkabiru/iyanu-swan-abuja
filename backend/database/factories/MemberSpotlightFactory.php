<?php

namespace Database\Factories;

use App\Models\MemberSpotlight;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MemberSpotlight>
 */
class MemberSpotlightFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name('female'),
            'quote' => fake()->sentence(15),
        ];
    }
}
