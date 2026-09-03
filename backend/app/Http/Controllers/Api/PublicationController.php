<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Http\JsonResponse;

class PublicationController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Publication::query()
                ->with('media')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->get()
                ->map(fn (Publication $publication): array => [
                    'id' => (string) $publication->id,
                    'title' => $publication->title,
                    'category' => $publication->category ?? 'Report',
                    'publishedAt' => $publication->published_at?->toDateString(),
                    'fileUrl' => $publication->file_url,
                    'sizeLabel' => $publication->size_label,
                ])
        );
    }
}
