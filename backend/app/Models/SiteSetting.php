<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SiteSetting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $appends = ['logo_url'];

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
        'membership_subscription_fee',
        'membership_welfare_fee',
        'active_payment_gateway',
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
            'membership_subscription_fee' => 'integer',
            'membership_welfare_fee' => 'integer',
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], ['chapter_name' => 'Chapter']);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo') ?: null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }
}
