<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MemberProfile extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $appends = ['photo_url'];

    protected $fillable = [
        'user_id',
        'membership_number',
        'credential',
        'membership_status',
        'phone',
        'date_of_birth',
        'sector',
        'specialisation',
        'year_admitted',
        'chapter_role',
        'cpd_target',
        'is_directory_listed',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'date',
            'date_of_birth' => 'date',
            'year_admitted' => 'integer',
            'cpd_target' => 'integer',
            'is_directory_listed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
