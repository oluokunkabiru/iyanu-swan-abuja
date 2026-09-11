<?php

namespace Database\Factories;

use App\Models\ProgrammeEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProgrammeEntry>
 */
class ProgrammeEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(5),
            'date_label' => fake()->date('j F Y'),
            'starts_at' => fake()->dateTimeBetween('now', '+6 months'),
            'ends_at' => null,
            'venue' => fake()->city(),
            'href' => '/events',
            'is_active' => true,
        ];
    }
}
