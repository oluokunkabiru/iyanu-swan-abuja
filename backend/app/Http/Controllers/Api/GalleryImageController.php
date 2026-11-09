<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\GalleryImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GalleryImageController extends Controller
{
    /**
     * The gallery is built from events: photos and videos uploaded on
     * the event itself, Gallery Images linked to an event, and the
     * event's video link. Each item carries its media type, event and
     * event type so the frontend can split photos from videos, group by
     * event and filter by type. Items not linked to any event are
     * grouped by their activity name, or posted under a "General" album
     * when they have none.
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
                media: $media,
                caption: (string) $media->getCustomProperty('caption', ''),
                event: $event,
            )))
            ->concat($event->galleryImages->map(fn (GalleryImage $image): array => $this->item(
                id: (string) $image->id,
                url: $image->image_url,
                media: $image->getFirstMedia('image'),
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
                ...$this->item((string) $image->id, $image->image_url, $image->getFirstMedia('image'), $image->caption ?? '', null),
                'album' => filled($image->album) ? $image->album : 'General',
                'year' => $image->year ?? $image->created_at->year,
            ]);

        return response()->json($eventPhotos->concat($unlinkedPhotos)->filter(fn (array $item): bool => filled($item['url']))->values());
    }

    /**
     * Sends a gallery file as a download. Browsers ignore the download
     * attribute on links to another domain, and the files are served from
     * the API's domain, so the frontend needs this endpoint to save them.
     */
    public function download(Media $media): Media
    {
        abort_unless($this->isPublicGalleryMedia($media), 404);
        abort_unless(Storage::disk($media->disk)->exists($media->getPathRelativeToRoot()), 404);

        return $media;
    }

    /**
     * Media ids are sequential across every model, including member photos
     * and payment evidence, so only files the gallery itself publishes
     * may be downloaded here.
     */
    private function isPublicGalleryMedia(Media $media): bool
    {
        $owner = $media->model;

        return match (true) {
            $owner instanceof Event => $media->collection_name === 'gallery' && $owner->status === 'published',
            $owner instanceof GalleryImage => $media->collection_name === 'image'
                && ($owner->event_id === null || $owner->event?->status === 'published'),
            default => false,
        };
    }

    /** @return array<string, mixed> */
    private function item(string $id, ?string $url, ?Media $media, string $caption, ?Event $event): array
    {
        return [
            'id' => $id,
            'url' => $url,
            'downloadUrl' => $media ? route('gallery.media.download', $media) : null,
            'mediaType' => str_starts_with((string) $media?->mime_type, 'video/') ? 'video' : 'image',
            'mimeType' => $media?->mime_type,
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
