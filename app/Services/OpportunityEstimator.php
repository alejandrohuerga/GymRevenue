<?php

namespace App\Services;

class OpportunityEstimator
{
    /**
     * Estimación conservadora de la oportunidad de recuperación mensual.
     *
     * Hipótesis de marketing: del valor mensual que representan los socios
     * inactivos y las bajas, estimamos que al menos la mitad es recuperable
     * con seguimiento. La fórmula se ajustará con datos reales.
     */
    public static function estimate(int $inactiveMembers, int $monthlyCancellations, float $averageFee): float
    {
        $monthlyValue = ($inactiveMembers + $monthlyCancellations) * $averageFee;

        return round($monthlyValue * 0.5, 2);
    }
}
