<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SiteSetting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $appends = ['logo_url', 'constitution_url'];

    protected $fillable = [
        'chapter_name',
        'short_name',
        'parent_body',
        'tagline',
        'mission',
        'mission_items',
        'vision',
        'aims',
        'address',
        'phone',
        'email',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'linkedin_url',
        'social_links',
        'hero_video_url',
        'chairperson_heading',
        'chairperson_message',
        'chapter_stats',
        'registration_steps',
        'member_benefits',
        'aims_objectives',
        'active_payment_gateway',
        'constitution_label',
    ];

    protected function casts(): array
    {
        return [
            'mission_items' => 'array',
            'social_links' => 'array',
            'chairperson_message' => 'array',
            'chapter_stats' => 'array',
            'registration_steps' => 'array',
            'member_benefits' => 'array',
            'aims_objectives' => 'array',
        ];
    }

    /**
     * This table only ever holds one row. See NotificationSetting::current()
     * for why keying off "the first row, or make one" (rather than an
     * explicit id=1 filter, which mass-assignment silently drops) is what
     * keeps this a genuine singleton.
     */
    public static function current(): self
    {
        return static::query()->first() ?? static::create(['chapter_name' => 'Chapter'])->refresh();
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo') ?: null;
    }

    public function getConstitutionUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('constitution') ?: null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('constitution')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
    }
}
