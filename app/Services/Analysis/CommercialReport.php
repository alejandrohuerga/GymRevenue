<?php

namespace App\Services\Analysis;

use App\Models\GymLeadAnalysis;
use Illuminate\Support\Carbon;

/**
 * Presentación comercial de los resultados del análisis.
 *
 * Convierte los resultados técnicos ya calculados por el motor en una
 * estructura orientada a negocio. NO recalcula métricas: solo traduce.
 * Toda la lógica matemática vive en el CsvAnalysisEngine.
 *
 * Este DTO trabaja con datos planos (array) para poder probarse sin
 * base de datos. El controlador lo alimenta desde el modelo persistido.
 */
final class CommercialReport
{
    private const QUALITY_LABELS = [
        'member_id_empty' => 'socios sin identificador (no analizados)',
        'member_id_duplicate' => 'identificadores duplicados (no contabilizados de nuevo)',
        'name_empty' => 'registros sin nombre (no analizados)',
        'status_invalid' => 'registros con estado no reconocido (no analizados)',
        'join_date_empty' => 'registros sin fecha de alta',
        'join_date_invalid' => 'registros con fecha de alta no válida',
        'join_date_future' => 'registros con una fecha de alta futura',
        'last_visit_empty' => 'registros sin fecha de última visita',
        'last_visit_invalid' => 'registros con fecha de última visita no válida',
        'last_visit_future' => 'registros con una fecha de última visita futura',
        'monthly_fee_invalid' => 'registros sin cuota mensual válida (no analizados)',
        'cancel_date_invalid' => 'registros con fecha de baja no válida',
        'cancel_before_join' => 'registros con fecha de baja anterior a la de alta',
    ];

    public function __construct(
        public readonly string $referenceDate,
        public readonly int $totalRecords,
        public readonly int $validRecords,
        public readonly int $recordsWithErrors,
        public readonly int $activeMembers,
        public readonly int $inactiveMembers,
        public readonly int $recentCancellations,
        public readonly int $activeLowActivity,
        public readonly int $activeHighRisk,
        public readonly ?float $averageMonthlyFee,
        public readonly float $monthlyValueAtRisk,
        public readonly float $reactivationPotential,
        public readonly array $opportunities,
        public readonly array $qualityErrors,
        public readonly bool $usable,
    ) {}

    public static function fromAnalysis(?GymLeadAnalysis $analysis): self
    {
        if ($analysis === null) {
            return self::empty();
        }

        return self::fromData($analysis->toArray());
    }

    /**
     * Construye el informe desde los datos persistidos del análisis.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromData(array $data): self
    {
        $usable = (int) ($data['members_valid'] ?? 0) > 0;

        return new self(
            referenceDate: Carbon::parse($data['reference_date'] ?? now())->format('d/m/Y'),
            totalRecords: (int) ($data['members_total'] ?? 0),
            validRecords: (int) ($data['members_valid'] ?? 0),
            recordsWithErrors: (int) ($data['members_with_errors'] ?? 0),
            activeMembers: (int) ($data['status_active'] ?? 0),
            inactiveMembers: (int) ($data['status_inactive'] ?? 0),
            recentCancellations: (int) ($data['cancellations_last_90_days'] ?? 0),
            activeLowActivity: (int) ($data['active_at_risk'] ?? 0) + (int) ($data['active_high_risk'] ?? 0),
            activeHighRisk: (int) ($data['active_high_risk'] ?? 0),
            averageMonthlyFee: isset($data['fees_average']) ? (float) $data['fees_average'] : null,
            monthlyValueAtRisk: (float) ($data['value_at_risk'] ?? 0),
            reactivationPotential: (float) ($data['reactivation_potential'] ?? 0),
            opportunities: $usable ? self::buildOpportunities($data) : [],
            qualityErrors: self::buildQualityErrors($data),
            usable: $usable,
        );
    }

    /**
     * Informe vacío para leads sin análisis.
     */
    private static function empty(): self
    {
        return new self(
            referenceDate: now()->format('d/m/Y'),
            totalRecords: 0,
            validRecords: 0,
            recordsWithErrors: 0,
            activeMembers: 0,
            inactiveMembers: 0,
            recentCancellations: 0,
            activeLowActivity: 0,
            activeHighRisk: 0,
            averageMonthlyFee: null,
            monthlyValueAtRisk: 0.0,
            reactivationPotential: 0.0,
            opportunities: [],
            qualityErrors: [],
            usable: false,
        );
    }

    /**
     * Oportunidades detectadas presentadas en lenguaje comercial.
     *
     * @param  array<string, mixed>  $d
     * @return array<int, array{key: string, title: string, text: string}>
     */
    private static function buildOpportunities(array $d): array
    {
        $list = [];

        $inactive = (int) ($d['status_inactive'] ?? 0);

        if ($inactive > 0) {
            $text = 'Hemos detectado '.self::countNoun($inactive, 'socio inactivo', 'socios inactivos')
                .' que podrían ser candidatos a una campaña de reactivación.';

            $reactivation = (float) ($d['reactivation_potential'] ?? 0);

            if ($reactivation > 0) {
                $text .= ' Esto representa hasta '.self::euro($reactivation)
                    .' de facturación potencial durante 3 meses, según las cuotas disponibles.';
            }

            $list[] = [
                'key' => 'inactive',
                'title' => 'Socios inactivos',
                'text' => $text,
            ];
        }

        $lowActivity = (int) ($d['active_at_risk'] ?? 0) + (int) ($d['active_high_risk'] ?? 0);

        if ($lowActivity > 0) {
            $text = 'Hemos detectado '.self::countNoun($lowActivity, 'socio activo', 'socios activos')
                .' que llevan más de 60 días sin registrar una visita. Podrían ser candidatos a acciones de seguimiento o reactivación antes de que su situación empeore.';

            $valueAtRisk = (float) ($d['value_at_risk'] ?? 0);

            if ($valueAtRisk > 0) {
                $text .= ' Cuotas mensuales asociadas a este grupo: '.self::euro($valueAtRisk)
                    .'/mes. Es una cifra independiente de la oportunidad de reactivación.';
            }

            $list[] = [
                'key' => 'low_activity',
                'title' => 'Socios activos con baja actividad',
                'text' => $text,
            ];
        }

        $highRisk = (int) ($d['active_high_risk'] ?? 0);

        if ($highRisk > 0) {
            $list[] = [
                'key' => 'high_risk',
                'title' => 'Socios con actividad muy baja',
                'text' => 'Hemos detectado '.self::countNoun($highRisk, 'socio activo', 'socios activos')
                    .' con más de 90 días sin registrar una visita. Conviene priorizar su seguimiento.',
            ];
        }

        $recentCancellations = (int) ($d['cancellations_last_90_days'] ?? 0);

        if ($recentCancellations > 0) {
            $list[] = [
                'key' => 'recent_cancellations',
                'title' => 'Bajas recientes',
                'text' => 'Hemos detectado '.self::countNoun($recentCancellations, 'baja', 'bajas')
                    .' durante los últimos 90 días. Este grupo puede ser especialmente interesante para analizar acciones de recuperación.',
            ];
        }

        return $list;
    }

    /**
     * Convierte los errores de calidad del motor en mensajes legibles y agrupados.
     *
     * @param  array<string, mixed>  $d
     * @return array<int, array{label: string, count: int}>
     */
    private static function buildQualityErrors(array $d): array
    {
        $quality = $d['quality'] ?? [];

        if (! is_array($quality) || $quality === []) {
            return [];
        }

        $grouped = [];

        foreach ($quality as $entry) {
            if (! is_array($entry)) {
                continue;
            }

            $type = (string) ($entry['type'] ?? '');
            $grouped[$type] = ($grouped[$type] ?? 0) + 1;
        }

        $labels = [];

        foreach ($grouped as $type => $count) {
            $labels[] = [
                'label' => self::QUALITY_LABELS[$type] ?? $type,
                'count' => $count,
            ];
        }

        return $labels;
    }

    private static function countNoun(int $value, string $singular, string $plural): string
    {
        return self::count($value).' '.($value === 1 ? $singular : $plural);
    }

    private static function count(int $value): string
    {
        return number_format($value, 0, ',', '.');
    }

    private static function euro(float $value): string
    {
        $s = number_format($value, 2, ',', '.');

        return (str_ends_with($s, ',00') ? substr($s, 0, -3) : $s).' €';
    }
}
