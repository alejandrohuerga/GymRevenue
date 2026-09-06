<?php

namespace Tests\Feature;

use App\Models\GymLead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class MemberCsvAnalysisTest extends TestCase
{
    use RefreshDatabase;

    private const SAMPLE_CSV = <<<'CSV'
member_id,name,status,join_date,last_visit,monthly_fee,cancel_date
1,Juan García,active,2025-03-15,2026-06-01,40.00,
2,Ana López,inactive,2024-10-10,2026-06-15,40.00,
3,Carlos Martín,cancelled,2023-05-20,2026-07-01,35.00,2026-08-01
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

    private function postCsv(string $content): TestResponse
    {
        return $this->post(route('lead.store'), [
            ...$this->validPayload(),
            'csv' => UploadedFile::fake()->createWithContent('socios.csv', $content),
        ]);
    }

    private function leadWithCsv(string $path = 'csv/muestra.csv'): GymLead
    {
        Storage::disk('local')->put($path, self::SAMPLE_CSV);

        return GymLead::create([
            'gym_name' => 'Fit Club',
            'contact_name' => 'Ana',
            'email' => 'ana@fitclub.test',
            'members' => 350,
            'average_fee' => 40,
            'inactive_members' => 30,
            'monthly_cancellations' => 15,
            'estimated_opportunity' => 330,
            'csv_path' => $path,
            'csv_original_name' => 'socios.csv',
            'csv_uploaded_at' => now(),
            'consent_at' => now(),
        ]);
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    public function test_analysis_is_created_after_csv_upload(): void
    {
        $response = $this->postCsv(self::SAMPLE_CSV);

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();

        $response->assertRedirect($lead->analysis->publicUrl());

        $this->assertDatabaseCount('gym_lead_analyses', 1);

        $this->assertDatabaseHas('gym_lead_analyses', [
            'members_total' => 3,
            'members_valid' => 3,
            'members_with_errors' => 0,
            'status_active' => 1,
            'status_inactive' => 1,
            'status_cancelled' => 1,
            'active_high_risk' => 1,
            'cancellations_last_90_days' => 1,
            'value_at_risk' => 40,
            'reactivation_potential' => 120,
        ]);
    }

    public function test_analysis_is_not_created_without_csv(): void
    {
        $this->post(route('lead.store'), $this->validPayload())
            ->assertRedirect(route('thanks'));

        $this->assertDatabaseCount('gym_lead_analyses', 0);
    }

    public function test_admin_can_see_analysis_metrics_in_lead_detail(): void
    {
        $this->postCsv(self::SAMPLE_CSV);

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.show', $lead))
            ->assertOk()
            ->assertSee('Análisis del CSV')
            ->assertSee('Registros totales')
            ->assertSee('40,00')
            ->assertSee('120,00')
            ->assertSee('Oportunidades detectadas');
    }

    public function test_admin_detail_shows_pending_when_analysis_is_missing(): void
    {
        $lead = $this->leadWithCsv();

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.show', $lead))
            ->assertOk()
            ->assertSee('Análisis pendiente')
            ->assertSee('Analizar CSV');
    }

    public function test_admin_can_run_analysis_on_demand(): void
    {
        $lead = $this->leadWithCsv();

        $this->actingAs($this->admin());

        $this->post(route('admin.leads.analyze', $lead))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseCount('gym_lead_analyses', 1);

        $this->assertDatabaseHas('gym_lead_analyses', [
            'gym_lead_id' => $lead->id,
            'members_total' => 3,
            'value_at_risk' => 40,
            'reactivation_potential' => 120,
        ]);
    }

    public function test_reanalysis_updates_existing_analysis(): void
    {
        $lead = $this->leadWithCsv();

        $this->actingAs($this->admin());

        $this->post(route('admin.leads.analyze', $lead))->assertRedirect();
        $this->post(route('admin.leads.analyze', $lead))->assertRedirect();

        $this->assertDatabaseCount('gym_lead_analyses', 1);
    }

    public function test_analyze_route_requires_authentication(): void
    {
        $lead = $this->leadWithCsv();

        $this->post(route('admin.leads.analyze', $lead))
            ->assertRedirect(route('admin.login'));

        $this->assertDatabaseCount('gym_lead_analyses', 0);
    }
}
