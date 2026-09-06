<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    'status',
    'notes',
    'csv_path',
    'csv_original_name',
    'csv_uploaded_at',
    'consent_at',
])]
class GymLead extends Model
{
    use HasFactory;

    public const STATUSES = [
        'new',
        'contacted',
        'interested',
        'proposal',
        'won',
        'lost',
    ];

    public const STATUS_LABELS = [
        'new' => 'Nuevo',
        'contacted' => 'Contactado',
        'interested' => 'Interesado',
        'proposal' => 'Propuesta',
        'won' => 'Ganado',
        'lost' => 'Perdido',
    ];

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
            'csv_uploaded_at' => 'datetime',
            'consent_at' => 'datetime',
        ];
    }

    public function analysis(): HasOne
    {
        return $this->hasOne(GymLeadAnalysis::class);
    }
}
