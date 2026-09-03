<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTicketType extends Model
{
    protected $fillable = ['event_id', 'label', 'price', 'currency'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
