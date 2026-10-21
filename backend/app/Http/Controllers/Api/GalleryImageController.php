<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\GalleryImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GalleryImageController extends Controller
{
    /**
     * The gallery is built from events: photos and videos uploaded on
     * the event itself, Gallery Images linked to an event, and the
     * event's video link. Each item carries its media type, event and
     * event type so the frontend can split photos from videos, group by
     * event and filter by type. Items not linked to any event are
     * posted under a single "General" album.
     */
    public function index(): JsonResponse
    {
        $events = Event::query()
            ->with(['media', 'galleryImages.media'])
            ->where('status', 'published')
            ->orderByDesc('starts_at')
            ->get();

        $eventPhotos = $events->flatMap(fn (Event $event): Collection => collect()
            ->concat($event->getMedia('gallery')->map(fn (Media $media): array => $this->item(
                id: 'event-media-'.$media->id,
                url: $media->getUrl(),
                mimeType: $media->mime_type,
                caption: (string) $media->getCustomProperty('caption', ''),
                event: $event,
            )))
            ->concat($event->galleryImages->map(fn (GalleryImage $image): array => $this->item(
                id: (string) $image->id,
                url: $image->image_url,
                mimeType: $image->getFirstMedia('image')?->mime_type,
                caption: $image->caption ?? '',
                event: $event,
            )))
            ->when(filled($event->video_url), fn (Collection $items): Collection => $items->push([
                ...$this->item('event-video-'.$event->id, $event->video_url, null, '', $event),
                'mediaType' => 'video',
                'isExternal' => true,
            ])));

        $unlinkedPhotos = GalleryImage::query()
            ->with('media')
            ->whereNull('event_id')
            ->orderByDesc('year')
            ->orderBy('caption')
            ->get()
            ->map(fn (GalleryImage $image): array => [
                ...$this->item((string) $image->id, $image->image_url, $image->getFirstMedia('image')?->mime_type, $image->caption ?? '', null),
                'album' => 'General',
                'year' => $image->year ?? $image->created_at->year,
            ]);

        return response()->json($eventPhotos->concat($unlinkedPhotos)->filter(fn (array $item): bool => filled($item['url']))->values());
    }

    /** @return array<string, mixed> */
    private function item(string $id, ?string $url, ?string $mimeType, string $caption, ?Event $event): array
    {
        return [
            'id' => $id,
            'url' => $url,
            'mediaType' => str_starts_with((string) $mimeType, 'video/') ? 'video' : 'image',
            'mimeType' => $mimeType,
            'isExternal' => false,
            'caption' => $caption,
            'album' => $event?->title,
            'year' => $event?->starts_at->year,
            'eventSlug' => $event?->slug,
            'eventTitle' => $event?->title,
            'eventType' => $event?->category,
            'eventDate' => $event?->starts_at->toDateString(),
        ];
    }
}
