<?php

namespace App\Services\Analysis;

use Carbon\CarbonInterface;

final readonly class AnalysisResult
{
    /**
     * Resultado estructurado del análisis de un CSV de socios.
     *
     * Las secciones intentan separar de forma clara:
     * - datos crudos (totales, ids);
     * - métricas calculadas (status, actividad, cancelaciones, antigüedad, cuotas);
     * - oportunidades económicas estimadas, mostradas por separado;
     * - calidad del CSV;
     * - oportunidades detectadas y mensajes para el usuario.
     */
    public function __construct(
        public CarbonInterface $referenceDate,
        public int $totalRows,
        public int $validRows,
        public int $rowsWithErrors,
        public int $uniqueMemberIds,
        public int $duplicateMemberIds,
        public array $statusCounts,
        public array $activityBuckets,
        public array $activeAtRisk,
        public array $inactiveActivity,
        public array $cancellations,
        public int $cancellationsLast90Days,
        public array $tenure,
        public ?float $averageFee,
        public float $feesActiveTotal,
        public float $feesInactiveTotal,
        public float $valueAtRisk,
        public float $reactivationPotential,
        public array $quality,
        public array $qualitySummary,
        public array $opportunities,
        public array $disclaimers,
    ) {}

    /**
     * Socios activos con más de 60 días desde su última visita.
     */
    public function activeOver60Days(): int
    {
        return $this->activeAtRisk['risk'] + $this->activeAtRisk['high_risk'];
    }

    /**
     * Socios activos con más de 90 días sin visita.
     */
    public function activeOver90Days(): int
    {
        return $this->activeAtRisk['high_risk'];
    }
}
