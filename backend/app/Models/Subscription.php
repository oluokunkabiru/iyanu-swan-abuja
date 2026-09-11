<?php

namespace App\Models;

use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Subscription extends Model implements HasMedia
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory;

    use InteractsWithMedia;

    protected $appends = ['evidence_url'];

    protected $fillable = [
        'user_id',
        'membership_level_id',
        'year',
        'subscription_amount',
        'welfare_amount',
        'status',
        'paid_at',
        'reference',
        'payment_gateway',
        'bank_transfer_reference',
        'review_note',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'subscription_amount' => 'integer',
            'welfare_amount' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membershipLevel(): BelongsTo
    {
        return $this->belongsTo(MembershipLevel::class);
    }

    public function getEvidenceUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('payment_evidence') ?: null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('payment_evidence')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf']);
    }
}
