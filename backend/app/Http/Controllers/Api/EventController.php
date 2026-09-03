<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Event::query()
            ->with('ticketTypes')
            ->where('status', 'published');

        if ($request->query('when') === 'past') {
            $query->where('starts_at', '<', now())->orderBy('starts_at', 'desc');
        } else {
            $query->where('starts_at', '>=', now())->orderBy('starts_at');
        }

        return response()->json($query->get());
    }

    public function show(Event $event): JsonResponse
    {
        abort_unless($event->status === 'published', 404);

        return response()->json($event->load('ticketTypes', 'galleryImages'));
    }
}
