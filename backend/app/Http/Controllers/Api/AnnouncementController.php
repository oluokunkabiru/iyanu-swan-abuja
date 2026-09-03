<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;

class AnnouncementController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Announcement::query()
                ->where('is_active', true)
                ->orderByDesc('published_on')
                ->orderByDesc('id')
                ->get()
                ->map(fn (Announcement $announcement): array => [
                    'id' => (string) $announcement->id,
                    'title' => $announcement->title,
                    'date' => $announcement->published_on->toDateString(),
                    'href' => $announcement->href ?? '/announcements',
                    'kind' => $announcement->kind,
                ])
        );
    }
}
