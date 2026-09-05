<?php

namespace Tests\Unit\Analysis;

use App\Services\Analysis\CsvAnalysisEngine;
use App\Services\Csv\CsvParser;
use App\Services\Csv\ParsedCsv;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class CsvAnalysisEngineTest extends TestCase
{
    private const REFERENCE_DATE = '2026-09-05';

    private CsvAnalysisEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->engine = new CsvAnalysisEngine;
    }

    private function reference(): Carbon
    {
        return Carbon::parse(self::REFERENCE_DATE);
    }

    private function analyze(string $content, ?string $referenceDate = self::REFERENCE_DATE)
    {
        $parsed = (new CsvParser)->parse($content);

        return $this->engine->analyze($parsed, $referenceDate ? Carbon::parse($referenceDate) : null);
    }

    private function analyzeRows(string $content)
    {
        return $this->analyze($content);
    }

    public function test_valid_csv_metrics(): void
    {
        $result = $this->analyze(<<<'CSV'
member_id,name,status,join_date,last_visit,monthly_fee
1,Juan García,active,2025-03-15,2026-09-01,40
CSV);

        $this->assertSame(1, $result->totalRows);
        $this->assertSame(1, $result->validRows);
        $this->assertSame(0, $result->rowsWithErrors);
        $this->assertSame(1, $result->uniqueMemberIds);
        $this->assertSame(['active' => 1, 'inactive' => 0, 'cancelled' => 0], $result->statusCounts);
        $this->assertSame(40.0, $result->averageFee);
        $this->assertSame(0.0, $result->valueAtRisk);
        $this->assertSame([], $result->opportunities);
        $this->assertSame([], $result->quality);
    }

    public function test_empty_csv(): void
    {
        $parsed = new ParsedCsv(',', ['member_id', 'name', 'status', 'join_date', 'last_visit'], []);

        $result = $this->engine->analyze($parsed, $this->reference());

        $this->assertSame(0, $result->totalRows);
        $this->assertSame(0, $result->validRows);
        $this->assertSame(0, $result->rowsWithErrors);
        $this->assertSame(['active' => 0, 'inactive' => 0, 'cancelled' => 0], $result->statusCounts);
        $this->assertSame([], $result->opportunities);
        $this->assertSame([], $result->quality);
    }

    public function test_csv_without_required_columns_marks_rows_as_invalid(): void
    {
        $parsed = new ParsedCsv(',', ['foo', 'bar'], [['1', 'x']]);

        $result = $this->engine->analyze($parsed, $this->reference());

        $this->assertSame(1, $result->totalRows);
        $this->assertSame(0, $result->validRows);
        $this->assertSame(1, $result->rowsWithErrors);
        $this->assertArrayHasKey('member_id_empty', $result->qualitySummary);
        $this->assertArrayHasKey('name_empty', $result->qualitySummary);
        $this->assertArrayHasKey('status_invalid', $result->qualitySummary);
    }

    public function test_duplicate_member_ids_are_detected_and_excluded(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit,monthly_fee
1,Juan,active,2025-01-01,2026-08-01,40
1,Juana Copia,active,2025-01-01,2026-08-01,40
CSV);

        $this->assertSame(1, $result->duplicateMemberIds);
        $this->assertSame(1, $result->uniqueMemberIds);
        $this->assertSame(1, $result->validRows);
        $this->assertSame(1, $result->rowsWithErrors);
        $this->assertSame(1, $result->statusCounts['active']);
    }

    public function test_invalid_status_is_reported_and_excluded(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit
2,Ana,pending,2025-01-01,2026-08-01
CSV);

        $this->assertSame(0, $result->validRows);
        $this->assertSame(1, $result->rowsWithErrors);
        $this->assertArrayHasKey('status_invalid', $result->qualitySummary);
        $this->assertSame(0, $result->statusCounts['active']);
    }

    public function test_invalid_date_is_detected(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit
3,Carlos,active,2025-01-01,2026-02-31
CSV);

        $this->assertSame(0, $result->validRows);
        $this->assertArrayHasKey('last_visit_invalid', $result->qualitySummary);
    }

    public function test_future_dates_are_reported(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit
4,Elena,active,2025-01-01,2026-10-15
5,Diego,active,2027-01-01,2026-08-01
CSV);

        $this->assertSame(0, $result->validRows);
        $this->assertArrayHasKey('last_visit_future', $result->qualitySummary);
        $this->assertArrayHasKey('join_date_future', $result->qualitySummary);
    }

    public function test_empty_required_date_is_invalid(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit
5,Carla,active,,2026-08-01
6,Dani,active,2025-01-01,
CSV);

        $this->assertSame(0, $result->validRows);
        $this->assertSame(2, $result->rowsWithErrors);
        $this->assertArrayHasKey('join_date_empty', $result->qualitySummary);
        $this->assertArrayHasKey('last_visit_empty', $result->qualitySummary);
    }

    public function test_dd_mm_yyyy_dates_are_accepted_and_normalized(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit,monthly_fee
7,Diego,active,15/01/2024,03/09/2026,40
CSV);

        $this->assertSame(1, $result->totalRows);
        $this->assertSame(1, $result->validRows);
        $this->assertSame(0, $result->rowsWithErrors);
        $this->assertSame(['active' => 1, 'inactive' => 0, 'cancelled' => 0], $result->statusCounts);
        $this->assertSame(40.0, $result->averageFee);
        $this->assertSame(['active_recent' => 1, 'attention' => 0, 'risk' => 0, 'high_risk' => 0], $result->activityBuckets);
        $this->assertSame([], $result->opportunities);
        $this->assertSame([], $result->quality);
    }

    public function test_impossible_dd_mm_yyyy_date_is_rejected(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit
8,Ana,active,15/01/2024,31/02/2026
CSV);

        $this->assertSame(0, $result->validRows);
        $this->assertArrayHasKey('last_visit_invalid', $result->qualitySummary);
    }

    public function test_yyyy_mm_dd_with_slashes_is_rejected(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit
9,Elena,active,2024/01/15,2026/09/03
CSV);

        $this->assertSame(0, $result->validRows);
        $this->assertArrayHasKey('join_date_invalid', $result->qualitySummary);
        $this->assertArrayHasKey('last_visit_invalid', $result->qualitySummary);
    }

    public function test_dd_mm_yyyy_cancel_date_is_accepted(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit,cancel_date
10,Sara,cancelled,15/01/2023,30/06/2026,15/08/2026
11,Ana,cancelled,15/01/2023,30/06/2026,
CSV);

        $this->assertSame(2, $result->statusCounts['cancelled']);
        $this->assertSame(2, $result->validRows);
        $this->assertSame(0, $result->rowsWithErrors);
        $this->assertSame(1, $result->cancellationsLast90Days);
        $this->assertSame(1, $result->cancellations['r0_30']);
    }

    public function test_cancel_date_before_join_date_is_a_quality_error(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit,cancel_date
12,Pedro,cancelled,2025-06-01,2026-08-01,2025-03-01
13,Laura,cancelled,15/06/2025,01/08/2026,01/03/2025
CSV);

        $this->assertSame(0, $result->validRows);
        $this->assertArrayHasKey('cancel_before_join', $result->qualitySummary);
        $this->assertSame(2, $result->qualitySummary['cancel_before_join']);
    }

    public function test_negative_monthly_fee_is_reported(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit,monthly_fee
6,Luis,active,2025-01-01,2026-08-01,-5
CSV);

        $this->assertSame(0, $result->validRows);
        $this->assertArrayHasKey('monthly_fee_invalid', $result->qualitySummary);
        $this->assertNull($result->averageFee);
    }

    public function test_days_since_last_visit_are_classified_into_buckets(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit
10,A,active,2024-01-01,2026-09-01
11,B,active,2024-01-01,2026-08-20
12,C,active,2024-01-01,2026-07-10
13,D,active,2024-01-01,2026-07-01
14,E,active,2024-01-01,2026-05-01
CSV);

        $this->assertSame([
            'active_recent' => 2,
            'attention' => 1,
            'risk' => 1,
            'high_risk' => 1,
        ], $result->activityBuckets);
    }

    public function test_active_members_over_60_days_without_visit_are_at_risk(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit
13,D,active,2024-01-01,2026-07-01
14,E,active,2024-01-01,2026-05-01
CSV);

        $this->assertSame(1, $result->activeAtRisk['risk']);
        $this->assertSame(1, $result->activeAtRisk['high_risk']);
        $this->assertSame(2, $result->activeOver60Days());
    }

    public function test_active_members_over_90_days_without_visit_are_high_risk(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit
14,E,active,2024-01-01,2026-05-01
CSV);

        $this->assertSame(1, $result->activeAtRisk['high_risk']);
        $this->assertSame(1, $result->activeOver90Days());
        $this->assertSame(0, $result->activeAtRisk['risk']);
    }

    public function test_inactive_members_are_not_counted_as_active_in_risk(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit,monthly_fee
20,Ana,inactive,2024-01-10,2026-06-15,40
CSV);

        $this->assertSame(1, $result->statusCounts['inactive']);
        $this->assertSame(1, $result->inactiveActivity['risk']);
        $this->assertSame(0, $result->activeAtRisk['risk']);
        $this->assertSame(0, $result->activeAtRisk['high_risk']);
        $this->assertSame(0.0, $result->valueAtRisk);
        $this->assertSame(120.0, $result->reactivationPotential);
    }

    public function test_cancelled_members_are_classified_by_cancel_date(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit,cancel_date
30,Carlos,cancelled,2023-05-20,2026-07-01,2026-08-01
31,Sara,cancelled,2023-05-20,2026-07-01,
CSV);

        $this->assertSame(2, $result->statusCounts['cancelled']);
        $this->assertSame(1, $result->cancellations['r31_90']);
        $this->assertSame(1, $result->cancellationsLast90Days);
        $this->assertSame(0, $result->activityBuckets['active_recent']);
    }

    public function test_cancellations_of_last_90_days(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit,cancel_date
40,A,cancelled,2023-01-01,2026-07-01,2026-08-25
41,B,cancelled,2023-01-01,2026-07-01,2026-06-10
42,C,cancelled,2023-01-01,2026-07-01,2025-12-01
CSV);

        $this->assertSame(1, $result->cancellations['r0_30']);
        $this->assertSame(1, $result->cancellations['r31_90']);
        $this->assertSame(0, $result->cancellations['r91_180']);
        $this->assertSame(1, $result->cancellations['r180']);
        $this->assertSame(2, $result->cancellationsLast90Days);
    }

    public function test_average_fee(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit,monthly_fee
50,A,active,2024-01-01,2026-08-01,30
51,B,active,2024-01-01,2026-08-01,40
52,C,active,2024-01-01,2026-08-01,50
CSV);

        $this->assertSame(40.0, $result->averageFee);
    }

    public function test_monthly_value_at_risk_only_counts_active_over_60_days(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit,monthly_fee
60,A,active,2024-01-01,2026-06-20,40
61,B,active,2024-01-01,2026-04-01,50
62,C,active,2024-01-01,2026-08-01,60
CSV);

        $this->assertSame(90.0, $result->valueAtRisk);
        $this->assertSame(1, $result->activeAtRisk['risk']);
        $this->assertSame(1, $result->activeAtRisk['high_risk']);
    }

    public function test_reactivation_potential_is_three_times_inactive_fees(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit,monthly_fee
70,A,inactive,2024-01-01,2026-07-01,30
71,B,inactive,2024-01-01,2026-07-01,40
CSV);

        $this->assertSame(210.0, $result->reactivationPotential);
    }

    public function test_value_at_risk_and_reactivation_are_not_mixed(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit,monthly_fee
80,A,active,2024-01-01,2026-06-20,50
81,B,inactive,2024-01-01,2026-07-01,40
CSV);

        $this->assertSame(50.0, $result->valueAtRisk);
        $this->assertSame(120.0, $result->reactivationPotential);

        $concatenated = implode(' ', $result->opportunities);

        $this->assertStringContainsString('socios inactivos', $concatenated);
        $this->assertStringContainsString('más de 60 días', $concatenated);
    }

    public function test_spanish_characters_are_handled(): void
    {
        $parsed = (new CsvParser)->parse(<<<'CSV'
member_id,name,status,join_date,last_visit
90,Muñoz Sánchez,active,2025-03-15,2026-08-20
91,Óscar Pérez,inactive,2025-03-15,2026-08-20
CSV);

        $result = $this->engine->analyze($parsed, $this->reference());

        $this->assertSame(2, $result->validRows);
        $this->assertSame(1, $result->statusCounts['active']);
        $this->assertSame(1, $result->statusCounts['inactive']);
    }

    public function test_comma_delimited_csv(): void
    {
        $result = $this->analyze(<<<'CSV'
member_id,name,status,join_date,last_visit,monthly_fee
100,Begoña,active,2025-01-01,2026-08-01,40
CSV);

        $this->assertSame(1, $result->validRows);
        $this->assertSame(40.0, $result->averageFee);
    }

    public function test_semicolon_delimited_csv(): void
    {
        $result = $this->analyze(<<<'CSV'
member_id;name;status;join_date;last_visit;monthly_fee
100;Begoña;active;2025-01-01;2026-08-01;40
CSV);

        $this->assertSame(1, $result->validRows);
        $this->assertSame(40.0, $result->averageFee);
    }

    public function test_csv_without_optional_columns(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit
110,Ana,active,2025-01-01,2026-08-01
111,Luis,inactive,2025-01-01,2026-08-01
CSV);

        $this->assertSame(2, $result->validRows);
        $this->assertNull($result->averageFee);
        $this->assertSame(0.0, $result->valueAtRisk);
        $this->assertSame(0.0, $result->reactivationPotential);
        $this->assertStringContainsString('socios inactivos', implode(' ', $result->opportunities));
    }

    public function test_tenure_buckets(): void
    {
        $result = $this->analyzeRows(<<<'CSV'
member_id,name,status,join_date,last_visit
120,A,active,2026-08-01,2026-08-20
121,B,active,2026-04-01,2026-08-20
122,C,active,2025-12-01,2026-08-20
123,D,active,2025-01-01,2026-08-20
124,E,active,2020-01-01,2026-08-20
CSV);

        $this->assertSame(1, $result->tenure['less_3m']);
        $this->assertSame(1, $result->tenure['3_6m']);
        $this->assertSame(1, $result->tenure['6_12m']);
        $this->assertSame(1, $result->tenure['1_2y']);
        $this->assertSame(1, $result->tenure['over_2y']);
    }
}
