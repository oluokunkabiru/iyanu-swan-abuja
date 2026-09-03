<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Firm extends Model
{
    /** @use HasFactory<\Database\Factories\FirmFactory> */
    use HasFactory;

    protected $fillable = [
        'principal_user_id',
        'name',
        'principal',
        'licence_number',
        'services',
        'area',
        'licence_status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'services' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function principalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'principal_user_id');
    }
}
