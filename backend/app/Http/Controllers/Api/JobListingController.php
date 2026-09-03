<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use Illuminate\Http\JsonResponse;

class JobListingController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            JobListing::query()
                ->where('is_active', true)
                ->orderByDesc('posted_at')
                ->orderByDesc('id')
                ->get()
                ->map(fn (JobListing $job): array => [
                    'id' => (string) $job->id,
                    'title' => $job->title,
                    'organisation' => $job->organisation,
                    'location' => $job->location,
                    'type' => $job->employment_type,
                    'level' => $job->seniority_level,
                    'postedAt' => $job->posted_at->toDateString(),
                    'closesAt' => $job->closes_at->toDateString(),
                    'summary' => $job->summary,
                    'applicationUrl' => $job->application_url,
                ])
        );
    }
}
