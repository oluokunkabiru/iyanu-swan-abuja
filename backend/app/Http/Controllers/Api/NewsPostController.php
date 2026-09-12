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
                ->with('media')
                ->where('is_published', true)
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->get()
                ->map(fn (NewsPost $post): array => $this->payload($post))
        );
    }

    public function show(NewsPost $newsPost): JsonResponse
    {
        abort_unless($newsPost->is_published, 404);

        $newsPost->load('media');

        return response()->json($this->payload($newsPost));
    }

    /** @return array<string, mixed> */
    private function payload(NewsPost $post): array
    {
        return [
            'id' => (string) $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt ?? '',
            'body' => $post->body ?? '',
            'category' => $post->category ?? 'Chapter',
            'publishedAt' => $post->published_at?->toDateString(),
            'author' => $post->author ?? 'SWAN Abuja Chapter',
            'coverUrl' => $post->cover_url,
        ];
    }
}
