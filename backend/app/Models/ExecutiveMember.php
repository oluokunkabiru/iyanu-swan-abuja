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

    protected static function booted(): void
    {
        static::saving(function (self $executiveMember): void {
            if (! $executiveMember->is_principal) {
                return;
            }

            $otherExecutiveMembers = static::query()->where('is_principal', true);

            if ($executiveMember->exists) {
                $otherExecutiveMembers->whereKeyNot($executiveMember->getKey());
            }

            $otherExecutiveMembers->update(['is_principal' => false]);
        });
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
