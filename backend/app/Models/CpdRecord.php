<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CpdRecord extends Model
{
    /** @use HasFactory<\Database\Factories\CpdRecordFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'activity',
        'activity_date',
        'hours',
        'activity_type',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'hours' => 'decimal:2',
            'is_verified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
