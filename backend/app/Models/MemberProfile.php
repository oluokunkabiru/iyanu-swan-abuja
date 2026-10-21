<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MemberProfile extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const TITLES = ['Mrs', 'Miss', 'Ms', 'Dr', 'Prof', 'Chief', 'Alhaja', 'Hon'];

    public const MARITAL_STATUSES = ['Single', 'Married', 'Divorced', 'Widowed', 'Separated'];

    public const NIGERIAN_STATES = [
        'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno',
        'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT', 'Gombe', 'Imo',
        'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos', 'Nasarawa',
        'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau', 'Rivers', 'Sokoto', 'Taraba',
        'Yobe', 'Zamfara',
    ];

    protected $appends = ['photo_url'];

    protected $fillable = [
        'user_id',
        'membership_number',
        'title',
        'former_name',
        'credential',
        'membership_status',
        'phone',
        'alternate_phone',
        'residential_address',
        'place_of_work',
        'date_of_birth',
        'marital_status',
        'nationality',
        'state_of_origin',
        'lga_of_origin',
        'next_of_kin_name',
        'next_of_kin_relationship',
        'next_of_kin_phone',
        'next_of_kin_address',
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
