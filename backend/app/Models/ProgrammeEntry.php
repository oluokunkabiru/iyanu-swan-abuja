<?php

namespace App\Models;

use Database\Factories\ProgrammeEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProgrammeEntry extends Model implements HasMedia
{
    /** @use HasFactory<ProgrammeEntryFactory> */
    use HasFactory, InteractsWithMedia;

    protected $appends = ['image_url', 'document_url'];

    protected $fillable = [
        'name',
        'description',
        'date_label',
        'starts_at',
        'ends_at',
        'venue',
        'href',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('document')
            ->singleFile()
            ->acceptsMimeTypes([
                'application/pdf',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('image') ?: null;
    }

    public function getDocumentUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('document') ?: null;
    }
}
