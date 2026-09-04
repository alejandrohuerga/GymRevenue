<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'gym_name',
    'contact_name',
    'email',
    'software',
    'members',
    'average_fee',
    'inactive_members',
    'monthly_cancellations',
    'estimated_opportunity',
    'consent_at',
])]
class GymLead extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'members' => 'integer',
            'average_fee' => 'decimal:2',
            'inactive_members' => 'integer',
            'monthly_cancellations' => 'integer',
            'estimated_opportunity' => 'decimal:2',
            'consent_at' => 'datetime',
        ];
    }
}
