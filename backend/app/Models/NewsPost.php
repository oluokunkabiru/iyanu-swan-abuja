<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class NewsPost extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $appends = ['cover_url'];

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'published_at',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('cover') ?: null;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
