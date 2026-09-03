<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberProfile extends Model
{
    protected $fillable = [
        'user_id',
        'membership_number',
        'credential',
        'membership_status',
        'phone',
        'sector',
        'specialisation',
        'year_admitted',
        'chapter_role',
        'cpd_target',
        'is_directory_listed',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'date',
            'year_admitted' => 'integer',
            'cpd_target' => 'integer',
            'is_directory_listed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
