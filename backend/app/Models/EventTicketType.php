<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTicketType extends Model
{
    protected $fillable = [
        'event_id',
        'label',
        'audience',
        'mode',
        'price',
        'currency',
        'includes',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'includes' => 'array',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
