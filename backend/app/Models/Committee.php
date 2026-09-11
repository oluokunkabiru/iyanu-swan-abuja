<?php

namespace App\Models;

use Database\Factories\CommitteeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    /** @use HasFactory<CommitteeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'remit',
        'chair',
        'focus_areas',
        'meeting_cadence',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'focus_areas' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
