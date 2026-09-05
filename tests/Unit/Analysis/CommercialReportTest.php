<?php

namespace Tests\Unit\Analysis;

use App\Services\Analysis\CommercialReport;
use PHPUnit\Framework\TestCase;

class CommercialReportTest extends TestCase
{
    private function report(array $overrides = []): CommercialReport
    {
        $data = [
            'reference_date' => '2026-09-05',
            'members_total' => 20,
            'members_valid' => 20,
            'members_with_errors' => 0,
            'status_active' => 10,
            'status_inactive' => 7,
            'status_cancelled' => 3,
            'active_at_risk' => 0,
            'active_high_risk' => 0,
            'cancellations_last_90_days' => 3,
            'fees_average' => 40.2,
            'value_at_risk' => 0,
            'reactivation_potential' => 833.7,
            'opportunities' => [],
            'quality' => [],
            'extra' => ['disclaimers' => []],
        ];

        return CommercialReport::fromData([...$data, ...$overrides]);
    }

    public function test_report_is_usable_when_valid_records_exist(): void
    {
        $report = $this->report();

        $this->assertTrue($report->usable);
        $this->assertSame(20, $report->validRecords);
        $this->assertSame('05/09/2026', $report->referenceDate);
        $this->assertSame(833.7, $report->reactivationPotential);
        $this->assertSame(0.0, $report->monthlyValueAtRisk);
    }

    public function test_inactive_and_recent_cancellations_build_opportunities(): void
    {
        $report = $this->report();

        $keys = array_column($report->opportunities, 'key');

        $this->assertSame(['inactive', 'recent_cancellations'], $keys);
        $this->assertStringContainsString('7 socios inactivos', $report->opportunities[0]['text']);
        $this->assertStringContainsString('833,70 €', $report->opportunities[0]['text']);
        $this->assertStringContainsString('3 bajas', $report->opportunities[1]['text']);
    }

    public function test_singular_and_plural_presentations(): void
    {
        $singular = $this->report([
            'status_active' => 10,
            'status_inactive' => 1,
            'status_cancelled' => 0,
            'cancellations_last_90_days' => 1,
            'reactivation_potential' => 39.9,
        ]);

        $this->assertStringContainsString('1 socio inactivo', $singular->opportunities[0]['text']);
        $this->assertStringContainsString('1 baja', $singular->opportunities[1]['text']);

        $plural = $this->report();

        $this->assertStringContainsString('7 socios inactivos', $plural->opportunities[0]['text']);
        $this->assertStringContainsString('3 bajas', $plural->opportunities[1]['text']);
    }

    public function test_value_at_risk_line_is_kept_separate_from_reactivation_potential(): void
    {
        $report = $this->report([
            'active_at_risk' => 3,
            'active_high_risk' => 0,
            'value_at_risk' => 250,
        ]);

        $lowActivity = $report->opportunities[1];

        $this->assertSame('low_activity', $lowActivity['key']);
        $this->assertStringContainsString('3 socios activos', $lowActivity['text']);
        $this->assertStringContainsString('250 €/mes', $lowActivity['text']);
        $this->assertStringContainsString('independiente de la oportunidad de reactivación', $lowActivity['text']);
    }

    public function test_high_risk_opportunity_is_independent(): void
    {
        $report = $this->report([
            'active_at_risk' => 0,
            'active_high_risk' => 2,
            'value_at_risk' => 0,
        ]);

        $keys = array_column($report->opportunities, 'key');

        $this->assertContains('high_risk', $keys);
        $high = $report->opportunities[array_search('high_risk', $keys, true)];

        $this->assertStringContainsString('2 socios activos', $high['text']);
    }

    public function test_quality_errors_are_grouped_with_friendly_labels(): void
    {
        $report = $this->report([
            'members_total' => 20,
            'members_valid' => 17,
            'members_with_errors' => 3,
            'quality' => [
                ['row' => 3, 'type' => 'last_visit_invalid', 'detail' => 'fecha de última visita inválida'],
                ['row' => 4, 'type' => 'last_visit_invalid', 'detail' => 'fecha de última visita inválida'],
                ['row' => 5, 'type' => 'join_date_empty', 'detail' => 'fecha de alta vacía'],
            ],
        ]);

        $this->assertCount(2, $report->qualityErrors);
        $this->assertSame('registros con fecha de última visita no válida', $report->qualityErrors[0]['label']);
        $this->assertSame(2, $report->qualityErrors[0]['count']);
        $this->assertSame('registros sin fecha de alta', $report->qualityErrors[1]['label']);
    }

    public function test_report_without_valid_records_is_not_usable(): void
    {
        $report = $this->report([
            'members_total' => 5,
            'members_valid' => 0,
            'members_with_errors' => 5,
        ]);

        $this->assertFalse($report->usable);
        $this->assertSame([], $report->opportunities);
    }

    public function test_null_analysis_produces_empty_report(): void
    {
        $report = CommercialReport::fromData([]);

        $this->assertFalse($report->usable);
        $this->assertSame(0, $report->totalRecords);
        $this->assertSame([], $report->opportunities);
    }

    public function test_amounts_do_not_show_unnecessary_decimals(): void
    {
        $report = $this->report(['reactivation_potential' => 300.0]);

        $this->assertStringContainsString('300 €', $report->opportunities[0]['text']);
        $this->assertStringNotContainsString('300,00 €', $report->opportunities[0]['text']);
    }
}
