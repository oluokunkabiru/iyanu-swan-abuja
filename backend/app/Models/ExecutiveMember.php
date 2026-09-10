<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ExecutiveMember extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $appends = ['photo_url'];

    protected $fillable = [
        'name',
        'credential',
        'position',
        'bio',
        'sort_order',
        'is_active',
        'is_principal',
        'term_start_year',
        'term_end_year',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_principal' => 'boolean',
            'term_start_year' => 'integer',
            'term_end_year' => 'integer',
        ];
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('photo') ?: null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }
}
