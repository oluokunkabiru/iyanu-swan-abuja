<?php

namespace Tests\Feature;

use App\Models\Event;
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
}
