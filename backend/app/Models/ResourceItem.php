<?php

namespace App\Models;

use Database\Factories\ResourceItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ResourceItem extends Model implements HasMedia
{
    /** @use HasFactory<ResourceItemFactory> */
    use HasFactory, InteractsWithMedia;

    protected $appends = ['file_url'];

    protected $fillable = [
        'title',
        'description',
        'category',
        'format',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('file')
            ->singleFile()
            ->acceptsMimeTypes([
                'application/pdf',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('file') ?: null;
    }
}
