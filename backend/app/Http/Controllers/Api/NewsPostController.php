<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use Illuminate\Http\JsonResponse;

class NewsPostController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            NewsPost::query()
                ->where('is_published', true)
                ->orderBy('published_at', 'desc')
                ->get()
        );
    }

    public function show(NewsPost $newsPost): JsonResponse
    {
        abort_unless($newsPost->is_published, 404);

        return response()->json($newsPost);
    }
}
