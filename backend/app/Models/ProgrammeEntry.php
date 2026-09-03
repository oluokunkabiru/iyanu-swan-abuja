<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgrammeEntry extends Model
{
    /** @use HasFactory<\Database\Factories\ProgrammeEntryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'date_label',
        'starts_at',
        'ends_at',
        'venue',
        'href',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
