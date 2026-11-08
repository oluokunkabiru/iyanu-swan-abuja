<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\GalleryImage;
use App\Models\User;
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

    public function test_items_without_an_event_or_activity_are_posted_under_general(): void
    {
        Storage::fake('public');
        GalleryImage::create(['caption' => 'Unlinked photo'])
            ->addMedia(UploadedFile::fake()->image('unlinked.jpg'))->toMediaCollection('image');

        $item = collect($this->getJson('/api/gallery')->assertOk()->json())->firstWhere('caption', 'Unlinked photo');

        $this->assertSame('General', $item['album']);
        $this->assertNull($item['eventSlug']);
    }

    public function test_items_without_an_event_are_grouped_by_their_activity(): void
    {
        Storage::fake('public');
        GalleryImage::create(['caption' => 'Activity photo', 'album' => 'Mentorship Day'])
            ->addMedia(UploadedFile::fake()->image('activity.jpg'))->toMediaCollection('image');

        $item = collect($this->getJson('/api/gallery')->assertOk()->json())->firstWhere('caption', 'Activity photo');

        $this->assertSame('Mentorship Day', $item['album']);
        $this->assertNull($item['eventSlug']);
    }

    public function test_gallery_item_downloads_as_an_attachment(): void
    {
        Storage::fake('public');
        $image = GalleryImage::create(['caption' => 'Keepsake']);
        $image->addMedia(UploadedFile::fake()->image('keepsake.jpg'))->toMediaCollection('image');

        $item = collect($this->getJson('/api/gallery')->assertOk()->json())->firstWhere('caption', 'Keepsake');

        $response = $this->get($item['downloadUrl'])->assertOk();

        $this->assertStringContainsString('attachment', (string) $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('keepsake.jpg', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_download_returns_not_found_when_the_file_is_gone(): void
    {
        Storage::fake('public');
        $image = GalleryImage::create(['caption' => 'Lost file']);
        $media = $image->addMedia(UploadedFile::fake()->image('lost.jpg'))->toMediaCollection('image');
        Storage::disk('public')->delete($media->getPathRelativeToRoot());

        $this->get(route('gallery.media.download', $media))->assertNotFound();
    }

    public function test_external_videos_have_no_download_link(): void
    {
        $this->publishedEvent(['video_url' => 'https://www.youtube.com/watch?v=abc123']);

        $item = collect($this->getJson('/api/gallery')->assertOk()->json())
            ->firstWhere('url', 'https://www.youtube.com/watch?v=abc123');

        $this->assertNull($item['downloadUrl']);
    }

    public function test_download_refuses_files_from_unpublished_events(): void
    {
        Storage::fake('public');
        $draft = $this->publishedEvent(['status' => 'draft']);
        $eventMedia = $draft->addMedia(UploadedFile::fake()->image('draft.jpg'))->toMediaCollection('gallery');
        $linkedImage = GalleryImage::create(['event_id' => $draft->id]);
        $linkedMedia = $linkedImage->addMedia(UploadedFile::fake()->image('linked.jpg'))->toMediaCollection('image');

        $this->get(route('gallery.media.download', $eventMedia))->assertNotFound();
        $this->get(route('gallery.media.download', $linkedMedia))->assertNotFound();
    }

    public function test_download_refuses_files_outside_the_gallery(): void
    {
        Storage::fake('public');
        $coverMedia = $this->publishedEvent()->addMedia(UploadedFile::fake()->image('cover.jpg'))->toMediaCollection('cover');

        $member = User::factory()->create(['role' => 'member']);
        $profile = $member->memberProfile()->create(['membership_status' => 'active']);
        $memberPhoto = $profile->addMedia(UploadedFile::fake()->image('member.jpg'))->toMediaCollection('photo');

        $this->get(route('gallery.media.download', $coverMedia))->assertNotFound();
        $this->get(route('gallery.media.download', $memberPhoto))->assertNotFound();
    }

    /** @param  array<string, mixed>  $attributes */
    private function publishedEvent(array $attributes = []): Event
    {
        return Event::create([
            'title' => 'Download Event',
            'slug' => 'download-event-test',
            'starts_at' => now()->subMonth(),
            'status' => 'published',
            ...$attributes,
        ]);
    }
}
