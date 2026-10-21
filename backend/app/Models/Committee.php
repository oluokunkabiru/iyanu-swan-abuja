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
        'subtitle',
        'purpose',
        'chair',
        'objectives',
        'structure',
        'meetings',
        'reporting',
        'note',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'objectives' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
