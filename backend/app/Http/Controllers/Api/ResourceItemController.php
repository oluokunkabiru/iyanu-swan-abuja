<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResourceItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class ResourceItemController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            ResourceItem::query()
                ->with('media')
                ->where('is_active', true)
                ->whereHas('media', fn (Builder $query): Builder => $query->where('collection_name', 'file'))
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (ResourceItem $resource): array => [
                    'id' => (string) $resource->id,
                    'title' => $resource->title,
                    'description' => $resource->description ?? '',
                    'category' => $resource->category,
                    'fileUrl' => $resource->file_url,
                    'format' => $resource->format,
                ])
        );
    }
}
