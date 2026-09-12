<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class ContactMessage extends Model
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'is_read',
        'reply_message',
        'replied_at',
        'replied_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'replied_at' => 'datetime',
        ];
    }

    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by_user_id');
    }
}
