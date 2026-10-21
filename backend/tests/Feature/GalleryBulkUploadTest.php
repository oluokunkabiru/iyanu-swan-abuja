<?php

namespace Tests\Feature;

use App\Filament\Resources\GalleryImages\Pages\ManageGalleryImages;
use App\Models\Event;
use App\Models\GalleryImage;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class GalleryBulkUploadTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_admin_can_upload_several_files_at_once_and_each_becomes_a_gallery_item(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $event = Event::create([
            'title' => 'Bulk Upload Event',
            'slug' => 'bulk-upload-event',
            'starts_at' => now()->subWeek(),
            'status' => 'published',
        ]);

        Livewire::actingAs(User::factory()->admin()->create())
            ->test(ManageGalleryImages::class)
            ->callAction(CreateAction::class, [
                'uploads' => [
                    UploadedFile::fake()->image('one.jpg'),
                    UploadedFile::fake()->image('two.jpg'),
                    UploadedFile::fake()->image('three.png'),
                ],
                'caption' => 'Group photo',
                'event_id' => $event->id,
            ])
            ->assertHasNoActionErrors();

        $items = GalleryImage::query()->where('event_id', $event->id)->get();

        $this->assertCount(3, $items);
        $this->assertSame(['Group photo'], $items->pluck('caption')->unique()->values()->all());
        $this->assertTrue($items->every(fn (GalleryImage $item): bool => $item->getFirstMedia('image') !== null));
    }
}
