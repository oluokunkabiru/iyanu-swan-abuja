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
        'tagline',
        'mission',
        'vision',
        'address',
        'phone',
        'email',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'hero_video_url',
        'membership_subscription_fee',
        'membership_welfare_fee',
    ];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], ['chapter_name' => 'Chapter']);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo') ?: null;
    }
}
