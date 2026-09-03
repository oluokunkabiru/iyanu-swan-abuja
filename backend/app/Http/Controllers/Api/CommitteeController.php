<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Committee;
use Illuminate\Http\JsonResponse;

class CommitteeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Committee::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (Committee $committee): array => $this->payload($committee))
        );
    }

    public function show(Committee $committee): JsonResponse
    {
        abort_unless($committee->is_active, 404);

        return response()->json($this->payload($committee));
    }

    /** @return array<string, mixed> */
    private function payload(Committee $committee): array
    {
        return [
            'id' => (string) $committee->id,
            'name' => $committee->name,
            'slug' => $committee->slug,
            'remit' => $committee->remit,
            'chair' => $committee->chair,
            'focusAreas' => $committee->focus_areas,
            'meetingCadence' => $committee->meeting_cadence,
        ];
    }
}
