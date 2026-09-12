<?php

namespace Tests\Feature;

use App\Models\GymLead;
use App\Services\Analysis\CsvAnalysisEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;

class PublicCsvSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private const PARTIAL_CSV = <<<'CSV'
member_id,name,status,join_date,last_visit
1,Juan García,active,2025-03-15,2026-08-20
2,Ana López,invalido,2024-10-10,2026-06-15
CSV;

    private const ALL_INVALID_CSV = <<<'CSV'
member_id,name,status,join_date,last_visit
1,,,,
2,,,,
CSV;

    private const ONE_MEMBER_CSV = <<<'CSV'
member_id,name,status,join_date,last_visit
1,Juan,active,2025-01-01,2026-08-01
CSV;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    private function validPayload(): array
    {
        return [
            'gym_name' => 'Fit Club',
            'contact_name' => 'Ana',
            'email' => 'ana@fitclub.test',
            'software' => 'Excel',
            'members' => 350,
            'average_fee' => 40,
            'inactive_members' => 30,
            'monthly_cancellations' => 15,
            'consent' => '1',
        ];
    }

    public function test_valid_csv_redirects_to_public_report_without_exposing_identity(): void
    {
        $content = (string) file_get_contents(base_path('tests/Fixtures/csv/socios_20.csv'));

        $response = $this->post(route('lead.store'), [
            ...$this->validPayload(),
            'csv' => UploadedFile::fake()->createWithContent('socios.csv', $content),
        ]);

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();
        $analysis = $lead->analysis;

        $this->assertNotNull($analysis);

        $response->assertRedirect($analysis->publicUrl());

        $url = $analysis->publicUrl();

        $this->assertMatchesRegularExpression('/\/analisis\/[A-Za-z0-9]{64}$/', $url);
        $this->assertStringNotContainsString('?', $url);
        $this->assertStringNotContainsString('ana@fitclub.test', $url);

        $this->assertDatabaseHas('gym_lead_analyses', [
            'gym_lead_id' => $lead->id,
            'members_total' => 20,
            'members_valid' => 20,
            'members_with_errors' => 0,
            'status_active' => 10,
            'status_inactive' => 7,
            'status_cancelled' => 3,
            'reactivation_potential' => 833.70,
        ]);

        $this->get($url)
            ->assertOk()
            ->assertSee('Análisis de tu gimnasio')
            ->assertSee('833,70')
            ->assertDontSee('ana@fitclub.test')
            ->assertDontSee('Fit Club');
    }

    public function test_without_csv_keeps_thanks_flow_and_does_not_create_analysis(): void
    {
        $response = $this->post(route('lead.store'), $this->validPayload());

        $response->assertRedirect(route('thanks'));

        $this->assertDatabaseCount('gym_leads', 1);
        $this->assertDatabaseCount('gym_lead_analyses', 0);
    }

    public function test_fully_invalid_csv_keeps_lead_and_returns_friendly_error(): void
    {
        $response = $this->from(route('calculator'))
            ->post(route('lead.store'), [
                ...$this->validPayload(),
                'csv' => UploadedFile::fake()->createWithContent('invalidos.csv', self::ALL_INVALID_CSV),
            ]);

        $response->assertRedirect(route('calculator'));
        $response->assertSessionHasErrors('csv');

        $this->assertStringContainsString('ninguna fila del CSV contiene datos válidos', (string) session('errors')->first('csv'));

        $this->assertDatabaseCount('gym_leads', 1);

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();

        $this->assertDatabaseHas('gym_lead_analyses', [
            'gym_lead_id' => $lead->id,
            'members_valid' => 0,
        ]);
    }

    public function test_partial_invalid_csv_redirects_to_report_with_quality_data(): void
    {
        $response = $this->post(route('lead.store'), [
            ...$this->validPayload(),
            'csv' => UploadedFile::fake()->createWithContent('parcial.csv', self::PARTIAL_CSV),
        ]);

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();

        $response->assertRedirect($lead->analysis->publicUrl());

        $this->assertDatabaseHas('gym_lead_analyses', [
            'gym_lead_id' => $lead->id,
            'members_total' => 2,
            'members_valid' => 1,
            'members_with_errors' => 1,
        ]);

        $this->get($lead->analysis->publicUrl())
            ->assertOk()
            ->assertSee('Análisis de tu gimnasio')
            ->assertSee('registros con estado no reconocido');
    }

    public function test_analysis_error_keeps_lead_and_returns_friendly_error(): void
    {
        $this->mock(CsvAnalysisEngine::class, function (MockInterface $mock): void {
            $mock->shouldReceive('analyze')->andThrow(new \RuntimeException('fallo simulado'));
        });

        $response = $this->from(route('calculator'))
            ->post(route('lead.store'), [
                ...$this->validPayload(),
                'csv' => UploadedFile::fake()->createWithContent('socios.csv', self::PARTIAL_CSV),
            ]);

        $response->assertRedirect(route('calculator'));
        $response->assertSessionHasErrors('csv');

        $this->assertStringContainsString('No hemos podido generar tu informe con este CSV', (string) session('errors')->first('csv'));

        $this->assertDatabaseCount('gym_leads', 1);
        $this->assertDatabaseCount('gym_lead_analyses', 0);
    }

    public function test_lead_created_signal_is_flashed_on_csv_report_redirect(): void
    {
        $content = (string) file_get_contents(base_path('tests/Fixtures/csv/socios_20.csv'));

        $response = $this->post(route('lead.store'), [
            ...$this->validPayload(),
            'csv' => UploadedFile::fake()->createWithContent('socios.csv', $content),
        ]);

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();

        $response->assertRedirect($lead->analysis->publicUrl());
        $this->assertTrue(session('lead_created'));

        $this->get($lead->analysis->publicUrl())
            ->assertOk()
            ->assertSee('leadCreated: true', false);
    }

    public function test_repeated_submit_creates_distinct_leads_with_one_analysis_each(): void
    {
        $payload = [
            ...$this->validPayload(),
            'csv' => UploadedFile::fake()->createWithContent('socios.csv', self::ONE_MEMBER_CSV),
        ];

        $first = $this->post(route('lead.store'), $payload);
        $second = $this->post(route('lead.store'), $payload);

        $leads = GymLead::where('email', 'ana@fitclub.test')->get();

        $this->assertCount(2, $leads);
        $this->assertDatabaseCount('gym_lead_analyses', 2);

        $first->assertRedirect($leads[0]->analysis->publicUrl());
        $second->assertRedirect($leads[1]->analysis->publicUrl());
    }
}
