<?php

namespace App\Models;

use App\Services\Analysis\AnalysisResult;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'gym_lead_id',
    'public_token',
    'reference_date',
    'members_total',
    'members_valid',
    'members_with_errors',
    'duplicate_member_ids',
    'status_active',
    'status_inactive',
    'status_cancelled',
    'active_at_risk',
    'active_high_risk',
    'cancellations_last_90_days',
    'fees_average',
    'value_at_risk',
    'reactivation_potential',
    'opportunities',
    'quality',
    'extra',
])]
class GymLeadAnalysis extends Model
{
    /**
     * Convierte un resultado del motor en la estructura de la tabla.
     *
     * @return array<string, mixed>
     */
    public static function fromResult(AnalysisResult $result): array
    {
        return [
            'reference_date' => $result->referenceDate->toDateString(),
            'members_total' => $result->totalRows,
            'members_valid' => $result->validRows,
            'members_with_errors' => $result->rowsWithErrors,
            'duplicate_member_ids' => $result->duplicateMemberIds,
            'status_active' => $result->statusCounts['active'],
            'status_inactive' => $result->statusCounts['inactive'],
            'status_cancelled' => $result->statusCounts['cancelled'],
            'active_at_risk' => $result->activeAtRisk['risk'],
            'active_high_risk' => $result->activeAtRisk['high_risk'],
            'cancellations_last_90_days' => $result->cancellationsLast90Days,
            'fees_average' => $result->averageFee,
            'value_at_risk' => $result->valueAtRisk,
            'reactivation_potential' => $result->reactivationPotential,
            'opportunities' => $result->opportunities,
            'quality' => $result->quality,
            'extra' => [
                'activity' => $result->activityBuckets,
                'inactive_activity' => $result->inactiveActivity,
                'cancellation_buckets' => $result->cancellations,
                'tenure' => $result->tenure,
                'disclaimers' => $result->disclaimers,
            ],
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reference_date' => 'date',
            'fees_average' => 'float',
            'value_at_risk' => 'float',
            'reactivation_potential' => 'float',
            'opportunities' => 'array',
            'quality' => 'array',
            'extra' => 'array',
            'public_token' => 'string',
        ];
    }

    public function gymLead(): BelongsTo
    {
        return $this->belongsTo(GymLead::class);
    }

    /**
     * Genera el token público automáticamente al crear el análisis.
     */
    protected static function booted(): void
    {
        static::creating(function (self $analysis): void {
            if ($analysis->public_token === null) {
                $analysis->public_token = self::generateAccessToken();
            }
        });
    }

    /**
     * Genera un token criptográficamente seguro, largo e impredecible.
     *
     * Se comprueba la unicidad antes de devolverlo porque la columna tiene
     * una restricción UNIQUE y no queremos depender de que una colisión sea
     * "prácticamente imposible": si ocurriera, se regenera.
     */
    public static function generateAccessToken(): string
    {
        do {
            $token = Str::random(64);
        } while (static::query()->where('public_token', $token)->exists());

        return $token;
    }

    /**
     * URL pública del informe comercial.
     */
    public function publicUrl(): string
    {
        return route('analysis.public.show', $this->public_token);
    }

    /**
     * Garantiza que el análisis dispone de token público.
     *
     * Permite que los análisis creados antes de introducir el token mantengan
     * su funcionamiento desde Admin y obtengan su enlace público bajo demanda.
     */
    public function ensurePublicToken(): string
    {
        if ($this->public_token === null) {
            $this->update(['public_token' => self::generateAccessToken()]);
            $this->refresh();
        }

        return (string) $this->public_token;
    }
}
