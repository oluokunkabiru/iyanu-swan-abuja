<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    /** @use HasFactory<\Database\Factories\CommitteeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'remit',
        'chair',
        'focus_areas',
        'meeting_cadence',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'focus_areas' => 'array',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
