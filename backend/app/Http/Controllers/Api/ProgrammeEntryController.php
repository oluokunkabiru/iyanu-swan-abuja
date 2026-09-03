<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgrammeEntry;
use Illuminate\Http\JsonResponse;

class ProgrammeEntryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            ProgrammeEntry::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (ProgrammeEntry $entry): array => [
                    'id' => (string) $entry->id,
                    'name' => $entry->name,
                    'date' => $entry->date_label,
                    'venue' => $entry->venue ?? '',
                    'href' => $entry->href ?? '/events',
                ])
        );
    }
}
