<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Publication extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $appends = ['file_url', 'size_label'];

    protected $fillable = ['title', 'category', 'published_at'];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('file') ?: null;
    }

    public function getSizeLabelAttribute(): ?string
    {
        $size = $this->getFirstMedia('file')?->size;

        if ($size === null) {
            return null;
        }

        return $size >= 1_048_576
            ? number_format($size / 1_048_576, 1).' MB'
            : number_format($size / 1_024).' KB';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('file')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
    }
}
