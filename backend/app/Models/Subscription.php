<?php

namespace App\Models;

use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'subscription_amount',
        'welfare_amount',
        'status',
        'paid_at',
        'reference',
        'payment_gateway',
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
}
