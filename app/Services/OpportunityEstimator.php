<?php

namespace App\Services;

class OpportunityEstimator
{
    public const RECOVERY_RATE_INACTIVE = 0.20;

    public const RECOVERY_RATE_CANCELLATION = 0.15;

    /**
     * Estimación mensual conservadora de la oportunidad de recuperación.
     *
     * Hipótesis iniciales de marketing (se ajustarán con datos reales):
     * - el 20% del valor mensual de los socios inactivos;
     * - el 15% del valor mensual de las bajas.
     */
    public static function estimate(int $inactiveMembers, int $monthlyCancellations, float $averageFee): float
    {
        $inactiveOpportunity = $inactiveMembers * $averageFee * self::RECOVERY_RATE_INACTIVE;
        $cancellationOpportunity = $monthlyCancellations * $averageFee * self::RECOVERY_RATE_CANCELLATION;

        return round($inactiveOpportunity + $cancellationOpportunity, 2);
    }

    /**
     * Proyección anual como referencia orientativa.
     */
    public static function estimateAnnual(int $inactiveMembers, int $monthlyCancellations, float $averageFee): float
    {
        return round(self::estimate($inactiveMembers, $monthlyCancellations, $averageFee) * 12, 2);
    }

    /**
     * Desglose del cálculo mensual.
     *
     * @return array<string, float>
     */
    public static function breakdown(int $inactiveMembers, int $monthlyCancellations, float $averageFee): array
    {
        return [
            'inactive' => round($inactiveMembers * $averageFee * self::RECOVERY_RATE_INACTIVE, 2),
            'cancellation' => round($monthlyCancellations * $averageFee * self::RECOVERY_RATE_CANCELLATION, 2),
        ];
    }
}
