<?php

namespace Tests\Feature;

use App\Models\GymLead;
use App\Models\GymLeadAnalysis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MemberCommercialReportTest extends TestCase
{
    use RefreshDatabase;

    private const FIXTURE_PATH = 'tests/Fixtures/csv/socios_20.csv';

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->travelTo(Carbon::parse('2026-09-05')->startOfDay());
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

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function lead(string $gymName = 'Fit Club', string $email = 'ana@fitclub.test'): GymLead
    {
        return GymLead::create([
            'gym_name' => $gymName,
            'contact_name' => 'Ana',
            'email' => $email,
            'members' => 350,
            'average_fee' => 40,
            'inactive_members' => 30,
            'monthly_cancellations' => 15,
            'estimated_opportunity' => 330,
            'consent_at' => now(),
        ]);
    }

    private function analysis(GymLead $lead, array $overrides = []): GymLeadAnalysis
    {
        $defaults = [
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

        return $lead->analysis()->create([...$defaults, ...$overrides]);
    }

    /**
     * Flujo real de 20 registros: CSV real (fichero del gimnasio) → análisis → informe.
     */
    public function test_report_from_real_20_record_csv(): void
    {
        $content = file_get_contents(base_path(self::FIXTURE_PATH));

        $response = $this->post(route('lead.store'), [
            ...$this->validPayload(),
            'csv' => UploadedFile::fake()->createWithContent('socios_20.csv', $content),
        ]);

        $response->assertRedirect(route('thanks'));

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();

        $this->assertDatabaseHas('gym_lead_analyses', [
            'gym_lead_id' => $lead->id,
            'members_total' => 20,
            'members_valid' => 20,
            'members_with_errors' => 0,
            'status_active' => 10,
            'status_inactive' => 7,
            'status_cancelled' => 3,
            'cancellations_last_90_days' => 3,
            'reactivation_potential' => 833.70,
        ]);

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.report', $lead))
            ->assertOk()
            ->assertSee('Análisis de tu gimnasio')
            ->assertSee('05/09/2026')
            ->assertSee('Potencial de reactivación')
            ->assertSee('833,70')
            ->assertSee('Hemos detectado 7 socios inactivos que podrían ser candidatos a una campaña de reactivación.')
            ->assertSee('Esto representa hasta 833,70 € de facturación potencial durante 3 meses, según las cuotas disponibles.')
            ->assertSee('Hemos detectado 3 bajas durante los últimos 90 días.')
            ->assertSee('No hemos detectado socios activos con más de 60 días sin registrar una visita.')
            ->assertSee('Se analizaron 20 registros y todos eran válidos.')
            ->assertDontSee('0,00')
            ->assertDontSee('Socios con actividad muy baja');
    }

    public function test_report_does_not_publish_member_personal_data(): void
    {
        $content = file_get_contents(base_path(self::FIXTURE_PATH));

        $this->post(route('lead.store'), [
            ...$this->validPayload(),
            'csv' => UploadedFile::fake()->createWithContent('socios_20.csv', $content),
        ]);

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.report', $lead))
            ->assertOk()
            ->assertDontSee('Juan García')
            ->assertDontSee('juan.garcia@example.com')
            ->assertDontSee('10001')
            ->assertDontSee('maria.sanchez@example.com');
    }

    public function test_report_hides_low_activity_block_when_not_present(): void
    {
        $lead = $this->lead();
        $this->analysis($lead);

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.report', $lead))
            ->assertOk()
            ->assertSee('Socios inactivos')
            ->assertSee('Bajas recientes')
            ->assertDontSee('Socios activos con baja actividad')
            ->assertDontSee('Socios con actividad muy baja');
    }

    public function test_report_shows_low_activity_block_and_independent_fee_line(): void
    {
        $lead = $this->lead();
        $this->analysis($lead, [
            'active_at_risk' => 5,
            'active_high_risk' => 0,
            'value_at_risk' => 250,
        ]);

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.report', $lead))
            ->assertOk()
            ->assertSee('Socios activos con baja actividad')
            ->assertSee('Hemos detectado 5 socios activos que llevan más de 60 días sin registrar una visita.')
            ->assertSee('Cuotas mensuales asociadas a este grupo: 250 €/mes')
            ->assertSee('Es una cifra independiente de la oportunidad de reactivación')
            ->assertDontSee('Socios con actividad muy baja');
    }

    public function test_report_shows_high_risk_block_when_active_over_90_days(): void
    {
        $lead = $this->lead();
        $this->analysis($lead, [
            'active_at_risk' => 0,
            'active_high_risk' => 6,
            'value_at_risk' => 0,
        ]);

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.report', $lead))
            ->assertOk()
            ->assertSee('Socios con actividad muy baja')
            ->assertSee('Hemos detectado 6 socios activos con más de 90 días sin registrar una visita.')
            ->assertSee('Conviene priorizar su seguimiento.');
    }

    public function test_report_shows_only_neutral_messages_when_there_are_no_opportunities(): void
    {
        $lead = $this->lead();
        $this->analysis($lead, [
            'status_active' => 20,
            'status_inactive' => 0,
            'status_cancelled' => 0,
            'cancellations_last_90_days' => 0,
            'reactivation_potential' => 0,
        ]);

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.report', $lead))
            ->assertOk()
            ->assertDontSee('Socios inactivos')
            ->assertDontSee('Bajas recientes')
            ->assertDontSee('Potencial de reactivación')
            ->assertDontSee('Cuotas de socios con baja actividad')
            ->assertSee('Quiero mejorar mi gimnasio')
            ->assertSee('No hemos detectado socios inactivos en el archivo.')
            ->assertSee('No hemos detectado bajas durante los últimos 90 días.')
            ->assertSee('No hemos detectado socios activos con más de 60 días sin registrar una visita.');
    }

    public function test_report_shows_data_quality_section_when_csv_has_errors(): void
    {
        $lead = $this->lead();
        $this->analysis($lead, [
            'members_total' => 20,
            'members_valid' => 17,
            'members_with_errors' => 3,
            'quality' => [
                ['row' => 3, 'type' => 'last_visit_invalid', 'detail' => 'fecha de última visita inválida'],
                ['row' => 4, 'type' => 'last_visit_invalid', 'detail' => 'fecha de última visita inválida'],
                ['row' => 5, 'type' => 'join_date_empty', 'detail' => 'fecha de alta vacía'],
            ],
        ]);

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.report', $lead))
            ->assertOk()
            ->assertSee('Calidad de los datos')
            ->assertSee('17 válidos')
            ->assertSee('3 con errores')
            ->assertSee('2×')
            ->assertSee('1×')
            ->assertSee('registros con fecha de última visita no válida')
            ->assertSee('registros sin fecha de alta')
            ->assertDontSee('last_visit_invalid');
    }

    public function test_report_shows_error_state_when_no_records_are_usable(): void
    {
        $lead = $this->lead();
        $this->analysis($lead, [
            'members_total' => 5,
            'members_valid' => 0,
            'members_with_errors' => 5,
            'status_active' => 0,
            'status_inactive' => 0,
            'status_cancelled' => 0,
            'quality' => [
                ['row' => 2, 'type' => 'status_invalid', 'detail' => 'status inválido'],
                ['row' => 3, 'type' => 'last_visit_invalid', 'detail' => 'fecha de última visita inválida'],
            ],
        ]);

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.report', $lead))
            ->assertOk()
            ->assertSee('No hemos podido generar el informe')
            ->assertSee('Calidad de los datos')
            ->assertDontSee('Potencial de reactivación')
            ->assertDontSee('Quiero mejorar mi gimnasio')
            ->assertSee('Volver al lead');
    }

    public function test_report_requires_admin_authentication(): void
    {
        $lead = $this->lead();
        $this->analysis($lead);

        $this->get(route('admin.leads.report', $lead))
            ->assertRedirect(route('admin.login'));
    }

    public function test_report_returns_not_found_without_analysis(): void
    {
        $lead = $this->lead();

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.report', $lead))
            ->assertNotFound();
    }

    public function test_admin_index_shows_analysis_state(): void
    {
        $withAnalysis = $this->lead('Fit Club', 'ana@fitclub.test');
        $this->analysis($withAnalysis);

        $pending = $this->lead('Power Gym', 'b@fitclub.test');
        $pending->update(['csv_path' => 'csv/pendiente.csv']);

        $invalid = $this->lead('Big Gym', 'c@fitclub.test');
        $this->analysis($invalid, [
            'members_valid' => 0,
            'members_with_errors' => 20,
            'status_active' => 0,
            'status_inactive' => 0,
            'status_cancelled' => 0,
        ]);
        $invalid->update(['csv_path' => 'csv/invalido.csv']);

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.index'))
            ->assertOk()
            ->assertSee('Disponible')
            ->assertSee(route('admin.leads.report', $withAnalysis))
            ->assertSee('Pendiente')
            ->assertSee('Sin datos válidos');
    }

    public function test_admin_lead_detail_links_to_report(): void
    {
        $lead = $this->lead();
        $this->analysis($lead);

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.show', $lead))
            ->assertOk()
            ->assertSee('Ver informe comercial')
            ->assertSee(route('admin.leads.report', $lead));
    }
}
