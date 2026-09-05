<?php

namespace App\Services\Analysis;

use App\Services\Csv\ParsedCsv;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class CsvAnalysisEngine
{
    public const STATUSES = ['active', 'inactive', 'cancelled'];

    public const QUALITY_LABELS = [
        'member_id_empty' => 'member_id vacío',
        'member_id_duplicate' => 'member_id duplicado (repetición no contabilizada)',
        'name_empty' => 'name vacío',
        'status_invalid' => 'status inválido',
        'join_date_empty' => 'fecha de alta vacía',
        'join_date_invalid' => 'fecha de alta inválida',
        'join_date_future' => 'fecha de alta futura',
        'last_visit_empty' => 'fecha de última visita vacía',
        'last_visit_invalid' => 'fecha de última visita inválida',
        'last_visit_future' => 'fecha de última visita futura',
        'monthly_fee_invalid' => 'cuota mensual inválida',
        'cancel_date_invalid' => 'fecha de baja inválida',
        'cancel_before_join' => 'fecha de baja anterior a la fecha de alta',
    ];

    /**
     * Analiza un CSV ya parseado y devuelve el resultado estructurado.
     *
     * La fecha de referencia es la fecha del servidor por defecto.
     * Los registros con errores de calidad quedan excluidos de los cálculos.
     */
    public function analyze(ParsedCsv $csv, ?CarbonInterface $referenceDate = null): AnalysisResult
    {
        $referenceDate = ($referenceDate ?? Carbon::now())->copy()->startOfDay();
        $columns = $this->buildColumnIndex($csv->header);

        $members = [];
        $seenIds = [];
        $duplicateRows = 0;

        foreach ($csv->rows as $rowIndex => $row) {
            $record = $this->normalize($row, $rowIndex + 2, $columns, $referenceDate, $seenIds);

            if ($record->duplicate) {
                $duplicateRows++;
            }

            $members[] = $record;
        }

        $valid = array_values(array_filter($members, static fn (MemberRecord $record) => $record->errors === []));

        $statusCounts = ['active' => 0, 'inactive' => 0, 'cancelled' => 0];
        $activityBuckets = ['active_recent' => 0, 'attention' => 0, 'risk' => 0, 'high_risk' => 0];
        $activeAtRisk = ['risk' => 0, 'high_risk' => 0];
        $inactiveActivity = ['active_recent' => 0, 'attention' => 0, 'risk' => 0, 'high_risk' => 0];
        $cancellations = ['r0_30' => 0, 'r31_90' => 0, 'r91_180' => 0, 'r180' => 0];
        $tenure = ['less_3m' => 0, '3_6m' => 0, '6_12m' => 0, '1_2y' => 0, 'over_2y' => 0];
        $fees = [];
        $feesActiveTotal = 0.0;
        $feesInactiveTotal = 0.0;
        $valueAtRisk = 0.0;

        foreach ($valid as $record) {
            $statusCounts[$record->status]++;

            if ($record->lastVisit !== null && $record->status !== 'cancelled') {
                $days = $record->lastVisit->diffInDays($referenceDate);
                $bucket = $this->activityBucket($days);
                $activityBuckets[$bucket]++;

                if ($record->status === 'inactive') {
                    $inactiveActivity[$bucket]++;
                }

                if ($record->status === 'active' && $days > 60) {
                    $activeAtRisk[$days > 90 ? 'high_risk' : 'risk']++;
                    $valueAtRisk += $record->monthlyFee ?? 0.0;
                }
            }

            if ($record->status === 'cancelled' && $record->cancelDate !== null) {
                $days = $record->cancelDate->diffInDays($referenceDate);
                $cancellations[$this->cancellationBucket($days)]++;
            }

            if ($record->joinDate !== null) {
                $tenure[$this->tenureBucket((int) $record->joinDate->diffInMonths($referenceDate))]++;
            }

            if ($record->monthlyFee !== null) {
                $fees[] = $record->monthlyFee;

                if ($record->status === 'active') {
                    $feesActiveTotal += $record->monthlyFee;
                }

                if ($record->status === 'inactive') {
                    $feesInactiveTotal += $record->monthlyFee;
                }
            }
        }

        $cancellationsLast90Days = $cancellations['r0_30'] + $cancellations['r31_90'];
        $averageFee = $fees === [] ? null : round(array_sum($fees) / count($fees), 2);
        $valueAtRisk = round($valueAtRisk, 2);
        $reactivationPotential = round($feesInactiveTotal * 3, 2);

        [$quality, $qualitySummary] = $this->buildQuality($members);

        return new AnalysisResult(
            referenceDate: $referenceDate,
            totalRows: count($members),
            validRows: count($valid),
            rowsWithErrors: count($members) - count($valid),
            uniqueMemberIds: count($seenIds),
            duplicateMemberIds: $duplicateRows,
            statusCounts: $statusCounts,
            activityBuckets: $activityBuckets,
            activeAtRisk: $activeAtRisk,
            inactiveActivity: $inactiveActivity,
            cancellations: $cancellations,
            cancellationsLast90Days: $cancellationsLast90Days,
            tenure: $tenure,
            averageFee: $averageFee,
            feesActiveTotal: round($feesActiveTotal, 2),
            feesInactiveTotal: round($feesInactiveTotal, 2),
            valueAtRisk: $valueAtRisk,
            reactivationPotential: $reactivationPotential,
            quality: $quality,
            qualitySummary: $qualitySummary,
            opportunities: $this->buildOpportunities($statusCounts, $activeAtRisk, $cancellationsLast90Days),
            disclaimers: $this->buildDisclaimers($statusCounts, $activeAtRisk, $reactivationPotential),
        );
    }

    /**
     * @return array<string, int>
     */
    private function buildColumnIndex(array $header): array
    {
        $indexes = [];

        foreach ($header as $position => $column) {
            $key = strtolower(trim((string) $column));

            if ($key !== '' && ! array_key_exists($key, $indexes)) {
                $indexes[$key] = $position;
            }
        }

        return $indexes;
    }

    /**
     * @param  array<string, int>  $columns
     * @param  array<string, true>  $seenIds
     */
    private function normalize(array $row, int $index, array $columns, CarbonInterface $referenceDate, array &$seenIds): MemberRecord
    {
        $cell = function (string $key) use ($row, $columns): string {
            $position = $columns[$key] ?? null;

            if ($position === null || ! array_key_exists($position, $row)) {
                return '';
            }

            return trim((string) $row[$position]);
        };

        $errors = [];

        $memberId = $cell('member_id');
        $name = $cell('name');
        $status = strtolower($cell('status'));
        $email = $cell('email');
        $feeValue = $cell('monthly_fee');

        $joinDate = $this->parseDate($cell('join_date'), 'join_date', $referenceDate, $errors);
        $lastVisit = $this->parseDate($cell('last_visit'), 'last_visit', $referenceDate, $errors);

        $cancelDate = null;
        $cancelValue = $cell('cancel_date');

        if ($cancelValue !== '') {
            $cancelDate = $this->parseDate($cancelValue, 'cancel_date', $referenceDate, $errors);
        }

        $monthlyFee = null;

        if ($feeValue !== '') {
            if (is_numeric($feeValue)) {
                $monthlyFee = (float) $feeValue;

                if ($monthlyFee < 0) {
                    $errors[] = 'monthly_fee_invalid';
                    $monthlyFee = null;
                }
            } else {
                $errors[] = 'monthly_fee_invalid';
            }
        }

        $duplicate = false;

        if ($memberId === '') {
            $errors[] = 'member_id_empty';
        } elseif (isset($seenIds[$memberId])) {
            $errors[] = 'member_id_duplicate';
            $duplicate = true;
        } else {
            $seenIds[$memberId] = true;
        }

        if ($name === '') {
            $errors[] = 'name_empty';
        }

        if (! in_array($status, self::STATUSES, true)) {
            $errors[] = 'status_invalid';
        }

        if ($joinDate !== null && $cancelDate !== null && $cancelDate->lt($joinDate)) {
            $errors[] = 'cancel_before_join';
        }

        return new MemberRecord(
            index: $index,
            memberId: $memberId,
            name: $name,
            email: $email !== '' ? $email : null,
            status: $status,
            joinDate: $joinDate,
            lastVisit: $lastVisit,
            monthlyFee: $monthlyFee,
            cancelDate: $cancelDate,
            errors: $errors,
            duplicate: $duplicate,
        );
    }

    /**
     * Interpreta una fecha de forma estricta.
     *
     * Formatos aceptados:
     * - YYYY-MM-DD (canónico de la especificación v1);
     * - DD/MM/YYYY (formato de exportación habitual, se normaliza internamente).
     *
     * @param  string[]  $errors
     */
    private function parseDate(string $value, string $key, CarbonInterface $referenceDate, array &$errors): ?CarbonInterface
    {
        if ($value === '') {
            $errors[] = $key.'_empty';

            return null;
        }

        $date = null;

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $date = $this->createStrictDate('!Y-m-d', $value);
        } elseif (preg_match('#^\d{2}/\d{2}/\d{4}$#', $value)) {
            $date = $this->createStrictDate('!d/m/Y', $value);
        }

        if ($date === null) {
            $errors[] = $key.'_invalid';

            return null;
        }

        if ($date->gt($referenceDate)) {
            $errors[] = $key.'_future';

            return null;
        }

        return $date;
    }

    /**
     * Crea una fecha comprobando que no exista desplazamiento ambiguo.
     *
     * Evita que fechas imposibles como 2026-02-31 o 31/02/2026 se conviertan
     * silenciosamente a otra fecha distinta.
     */
    private function createStrictDate(string $format, string $value): ?CarbonInterface
    {
        $date = Carbon::createFromFormat($format, $value);

        if ($date === false || $date->format(ltrim($format, '!')) !== $value) {
            return null;
        }

        return $date;
    }

    private function activityBucket(int $days): string
    {
        return match (true) {
            $days <= 30 => 'active_recent',
            $days <= 60 => 'attention',
            $days <= 90 => 'risk',
            default => 'high_risk',
        };
    }

    private function cancellationBucket(int $days): string
    {
        return match (true) {
            $days <= 30 => 'r0_30',
            $days <= 90 => 'r31_90',
            $days <= 180 => 'r91_180',
            default => 'r180',
        };
    }

    private function tenureBucket(int $months): string
    {
        return match (true) {
            $months < 3 => 'less_3m',
            $months < 6 => '3_6m',
            $months < 12 => '6_12m',
            $months <= 24 => '1_2y',
            default => 'over_2y',
        };
    }

    /**
     * @param  MemberRecord[]  $members
     * @return array{0: array<int, array{row: int, type: string, detail: string}>, 1: array<string, int>}
     */
    private function buildQuality(array $members): array
    {
        $quality = [];
        $summary = [];

        foreach ($members as $record) {
            foreach ($record->errors as $type) {
                $quality[] = [
                    'row' => $record->index,
                    'type' => $type,
                    'detail' => self::QUALITY_LABELS[$type] ?? $type,
                ];
                $summary[$type] = ($summary[$type] ?? 0) + 1;
            }
        }

        return [$quality, $summary];
    }

    /**
     * @param  array<string, int>  $statusCounts
     * @param  array<string, int>  $activeAtRisk
     * @return string[]
     */
    private function buildOpportunities(array $statusCounts, array $activeAtRisk, int $cancellationsLast90Days): array
    {
        $opportunities = [];

        if ($statusCounts['inactive'] > 0) {
            $opportunities[] = 'Se han detectado '.$statusCounts['inactive']
                .' socios inactivos que podrían ser candidatos a una campaña de reactivación.';
        }

        $activeOver60 = $activeAtRisk['risk'] + $activeAtRisk['high_risk'];

        if ($activeOver60 > 0) {
            $opportunities[] = 'Se han detectado '.$activeOver60.' socios activos con más de 60 días desde su última visita.';
        }

        if ($activeAtRisk['high_risk'] > 0) {
            $opportunities[] = $activeAtRisk['high_risk'].' socios llevan más de 90 días sin registrar actividad.';
        }

        if ($cancellationsLast90Days > 0) {
            $opportunities[] = $cancellationsLast90Days.' socios han causado baja durante los últimos 90 días.';
        }

        return $opportunities;
    }

    /**
     * @param  array<string, int>  $statusCounts
     * @param  array<string, int>  $activeAtRisk
     * @return string[]
     */
    private function buildDisclaimers(array $statusCounts, array $activeAtRisk, float $reactivationPotential): array
    {
        $disclaimers = [
            'Estimación orientativa basada en los datos proporcionados; no constituye una garantía de recuperación.',
            'La clasificación de actividad no implica necesariamente que un socio vaya a darse de baja.',
        ];

        $activeOver60 = $activeAtRisk['risk'] + $activeAtRisk['high_risk'];

        if ($activeOver60 > 0) {
            $disclaimers[] = 'Cuotas mensuales asociadas a socios con baja actividad (socios activos con más de 60 días desde su última visita).';
        }

        if ($statusCounts['inactive'] > 0 && $reactivationPotential > 0) {
            $disclaimers[] = 'Hasta '.number_format($reactivationPotential, 0, ',', '.')
                .' € de facturación potencial durante 3 meses si se consiguiera reactivar este grupo (estimación teórica).';
        }

        return $disclaimers;
    }
}
