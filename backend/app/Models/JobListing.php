<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobListing extends Model
{
    /** @use HasFactory<\Database\Factories\JobListingFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'organisation',
        'location',
        'employment_type',
        'seniority_level',
        'posted_at',
        'closes_at',
        'summary',
        'application_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'posted_at' => 'date',
            'closes_at' => 'date',
            'is_active' => 'boolean',
        ];
    }
}
