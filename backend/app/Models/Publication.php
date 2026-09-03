<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Publication extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $appends = ['file_url'];

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
}
