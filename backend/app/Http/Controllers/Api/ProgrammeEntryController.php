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
                ->with('media')
                ->where('is_active', true)
                ->orderBy('starts_at')
                ->get()
                ->map(fn (ProgrammeEntry $entry): array => [
                    'id' => (string) $entry->id,
                    'name' => $entry->name,
                    'description' => $entry->description,
                    'date' => $entry->date_label,
                    'venue' => $entry->venue ?? '',
                    'href' => $entry->href ?? '/events',
                    'imageUrl' => $entry->image_url,
                    'documentUrl' => $entry->document_url,
                ])
        );
    }
}
