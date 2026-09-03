<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    /** @use HasFactory<\Database\Factories\TrainingFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'provider',
        'delivery_mode',
        'starts_on',
        'cpd_hours',
        'fee',
        'member_fee',
        'seats_available',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'cpd_hours' => 'integer',
            'fee' => 'integer',
            'member_fee' => 'integer',
            'seats_available' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
