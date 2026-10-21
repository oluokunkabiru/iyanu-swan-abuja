<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class GalleryImage extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const IMAGE_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    public const VIDEO_MIME_TYPES = ['video/mp4', 'video/webm', 'video/quicktime'];

    /** Largest photo or video an admin can upload, in kilobytes (100 MB). */
    public const MAX_UPLOAD_KB = 102400;

    protected $appends = ['image_url'];

    protected $fillable = ['caption', 'album', 'year', 'event_id'];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function getIsVideoAttribute(): bool
    {
        return str_starts_with((string) $this->getFirstMedia('image')?->mime_type, 'video/');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('image') ?: null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->acceptsMimeTypes([...self::IMAGE_MIME_TYPES, ...self::VIDEO_MIME_TYPES]);
    }
}
