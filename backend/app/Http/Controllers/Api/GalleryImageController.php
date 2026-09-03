<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\JsonResponse;

class GalleryImageController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            GalleryImage::query()
                ->with('media')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (GalleryImage $image): array => [
                    'id' => (string) $image->id,
                    'caption' => $image->caption ?? '',
                    'album' => $image->album ?? 'Chapter life',
                    'year' => $image->year ?? $image->created_at->year,
                    'imageUrl' => $image->image_url,
                ])
        );
    }
}
