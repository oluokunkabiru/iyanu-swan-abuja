<?php

namespace Tests\Feature;

use App\Models\JobListing;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class JobListingControllerTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_open_jobs_only_include_active_roles_that_close_today_or_later(): void
    {
        $openJob = JobListing::factory()->create(['closes_at' => today()]);
        $expiredJob = JobListing::factory()->create(['closes_at' => today()->subDay()]);
        $inactiveJob = JobListing::factory()->create(['closes_at' => today()->addDay(), 'is_active' => false]);

        $this->getJson('/api/jobs')
            ->assertOk()
            ->assertJsonFragment(['id' => (string) $openJob->id])
            ->assertJsonMissing(['id' => (string) $expiredJob->id])
            ->assertJsonMissing(['id' => (string) $inactiveJob->id]);
    }

    public function test_closed_jobs_include_expired_active_roles(): void
    {
        $expiredJob = JobListing::factory()->create(['closes_at' => today()->subDay()]);
        $openJob = JobListing::factory()->create(['closes_at' => today()->addDay()]);
        $inactiveExpiredJob = JobListing::factory()->create(['closes_at' => today()->subDay(), 'is_active' => false]);

        $this->getJson('/api/jobs?status=closed')
            ->assertOk()
            ->assertJsonFragment(['id' => (string) $expiredJob->id])
            ->assertJsonMissing(['id' => (string) $openJob->id])
            ->assertJsonMissing(['id' => (string) $inactiveExpiredJob->id]);
    }
}
