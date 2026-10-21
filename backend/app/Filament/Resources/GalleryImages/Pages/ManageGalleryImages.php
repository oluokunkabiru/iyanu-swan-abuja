<?php

namespace App\Filament\Resources\GalleryImages\Pages;

use App\Filament\Resources\GalleryImages\GalleryImageResource;
use App\Models\GalleryImage;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\DB;

class ManageGalleryImages extends ManageRecords
{
    protected static string $resource = GalleryImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Upload photos & videos')
                ->modalHeading('Upload photos & videos')
                ->successNotificationTitle('Uploaded to the gallery')
                ->using(fn (array $data): GalleryImage => $this->createOnePerFile($data)),
        ];
    }

    /**
     * The upload field takes many files at once; each becomes its own
     * gallery item sharing the chosen event and caption.
     *
     * @param  array{uploads: array<int, string>, caption?: ?string, event_id?: ?int}  $data
     */
    private function createOnePerFile(array $data): GalleryImage
    {
        return DB::transaction(function () use ($data): GalleryImage {
            $created = null;

            foreach ($data['uploads'] as $path) {
                $created = GalleryImage::query()->create([
                    'caption' => $data['caption'] ?? null,
                    'event_id' => $data['event_id'] ?? null,
                ]);

                $created->addMediaFromDisk($path, 'local')->toMediaCollection('image');
            }

            return $created;
        });
    }
}
