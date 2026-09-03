<?php

namespace Database\Factories;

use App\Models\JobListing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobListing>
 */
class JobListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'organisation' => fake()->company(),
            'location' => fake()->city(),
            'employment_type' => fake()->randomElement(['Full-time', 'Contract', 'Part-time']),
            'seniority_level' => fake()->randomElement(['Entry', 'Mid', 'Senior', 'Executive']),
            'posted_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'closes_at' => fake()->dateTimeBetween('now', '+2 months'),
            'summary' => fake()->paragraph(),
            'application_url' => fake()->url(),
            'is_active' => true,
        ];
    }
}
