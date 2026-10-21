<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\GalleryImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class EventGalleryTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_published_event_returns_all_of_its_gallery_images(): void
    {
        Storage::fake('public');
        $event = Event::create([
            'title' => 'Chapter Summit',
            'slug' => 'chapter-summit',
            'summary' => 'The annual chapter summit.',
            'starts_at' => now()->addWeek(),
            'status' => 'published',
        ]);
        $event->addMedia(UploadedFile::fake()->image('first-gallery-image.jpg', 1200, 800))
            ->toMediaCollection('gallery');
        $event->addMedia(UploadedFile::fake()->image('second-gallery-image.jpg', 1200, 800))
            ->toMediaCollection('gallery');

        $response = $this->getJson('/api/events/chapter-summit');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'galleryImageUrls');

        $this->assertStringContainsString('first-gallery-image.jpg', $response->json('galleryImageUrls.0'));
        $this->assertStringContainsString('second-gallery-image.jpg', $response->json('galleryImageUrls.1'));
    }

    public function test_gallery_is_built_from_event_photos_tagged_with_event_and_type(): void
    {
        Storage::fake('public');
        $event = Event::create([
            'title' => 'Kwali Outreach',
            'slug' => 'kwali-outreach-test',
            'category' => 'Outreach',
            'starts_at' => now()->subMonth(),
            'status' => 'published',
        ]);
        $event->addMedia(UploadedFile::fake()->image('on-event.jpg'))->toMediaCollection('gallery');
        $event->addMedia(UploadedFile::fake()->createWithContent('clip.mp4', "\x00\x00\x00\x18ftypmp42\x00\x00\x00\x00mp42isom".str_repeat("\x00", 64)))->toMediaCollection('gallery');
        $event->update(['video_url' => 'https://www.youtube.com/watch?v=abc123']);
        GalleryImage::create(['caption' => 'Linked photo', 'event_id' => $event->id])
            ->addMedia(UploadedFile::fake()->image('linked.jpg'))->toMediaCollection('image');

        $photos = collect($this->getJson('/api/gallery')->assertOk()->json())
            ->where('eventSlug', 'kwali-outreach-test');

        $this->assertCount(4, $photos);
        $this->assertSame(['Outreach'], $photos->pluck('eventType')->unique()->values()->all());
        $this->assertContains('Linked photo', $photos->pluck('caption'));
        $this->assertSame(2, $photos->where('mediaType', 'video')->count());
        $this->assertTrue($photos->firstWhere('url', 'https://www.youtube.com/watch?v=abc123')['isExternal']);

        $this->getJson('/api/events/kwali-outreach-test')
            ->assertJsonCount(2, 'galleryImageUrls')
            ->assertJsonPath('galleryVideoCount', 2);
    }

    public function test_items_without_an_event_are_posted_under_general(): void
    {
        Storage::fake('public');
        GalleryImage::create(['caption' => 'Unlinked photo', 'album' => 'Old album name'])
            ->addMedia(UploadedFile::fake()->image('unlinked.jpg'))->toMediaCollection('image');

        $item = collect($this->getJson('/api/gallery')->assertOk()->json())->firstWhere('caption', 'Unlinked photo');

        $this->assertSame('General', $item['album']);
        $this->assertNull($item['eventSlug']);
    }
}
