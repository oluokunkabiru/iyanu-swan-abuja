<?php

namespace App\Models;

use Database\Factories\AnnouncementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    /** @use HasFactory<AnnouncementFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'published_on',
        'href',
        'kind',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'published_on' => 'date',
            'is_active' => 'boolean',
        ];
    }
}
