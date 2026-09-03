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
            ->with(['ticketTypes', 'media'])
            ->where('status', 'published');

        if ($request->query('when') === 'past') {
            $query->where('starts_at', '<', now())->orderBy('starts_at', 'desc');
        } else {
            $query->where('starts_at', '>=', now())->orderBy('starts_at');
        }

        return response()->json(
            $query->get()->map(fn (Event $event): array => $this->payload($event))
        );
    }

    public function show(Event $event): JsonResponse
    {
        abort_unless($event->status === 'published', 404);

        $event->load(['ticketTypes', 'media']);

        return response()->json($this->payload($event));
    }

    /** @return array<string, mixed> */
    private function payload(Event $event): array
    {
        return [
            'id' => (string) $event->id,
            'title' => $event->title,
            'slug' => $event->slug,
            'summary' => $event->summary ?? $event->description ?? '',
            'body' => $event->body ?? array_filter([$event->description]),
            'category' => $event->category ?? 'Meeting',
            'venue' => $event->location ?? '',
            'startsAt' => $event->starts_at->toIso8601String(),
            'endsAt' => $event->ends_at?->toIso8601String(),
            'cpdHours' => $event->cpd_hours,
            'isFeatured' => $event->is_featured,
            'status' => $event->starts_at->isPast() ? 'past' : 'upcoming',
            'coverUrl' => $event->cover_url,
            'ticketTiers' => $event->ticketTypes->map(fn ($ticket): array => [
                'id' => (string) $ticket->id,
                'audience' => $ticket->audience ?? 'member',
                'mode' => $ticket->mode ?? 'physical',
                'label' => $ticket->label,
                'price' => $ticket->price,
                'includes' => $ticket->includes ?? [],
            ])->values(),
            'speakers' => $event->speakers ?? [],
        ];
    }
}
