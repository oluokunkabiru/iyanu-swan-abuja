<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    /** @use HasFactory<\Database\Factories\AnnouncementFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'published_on',
        'href',
        'kind',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'published_on' => 'date',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
