<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobListingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $isClosed = $request->query('status') === 'closed';

        return response()->json(
            JobListing::query()
                ->where('is_active', true)
                ->when(
                    $isClosed,
                    fn ($query) => $query->whereDate('closes_at', '<', today()),
                    fn ($query) => $query->whereDate('closes_at', '>=', today()),
                )
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
