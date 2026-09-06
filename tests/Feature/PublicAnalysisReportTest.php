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

class PublicAnalysisReportTest extends TestCase
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

    public function test_1_public_report_is_accessible_with_valid_token(): void
    {
        $lead = $this->lead();
        $analysis = $this->analysis($lead);

        $this->get(route('analysis.public.show', $analysis->public_token))
            ->assertOk()
            ->assertSee('Análisis de tu gimnasio');
    }

    public function test_2_public_report_returns_404_for_unknown_token(): void
    {
        $this->get(route('analysis.public.show', 'token-que-no-existe'))
            ->assertNotFound()
            ->assertDontSee('Fit Club')
            ->assertDontSee('ana@fitclub.test');
    }

    public function test_3_public_report_does_not_require_authentication(): void
    {
        $lead = $this->lead();
        $analysis = $this->analysis($lead);

        $this->assertGuest();

        $this->get(route('analysis.public.show', $analysis->public_token))
            ->assertOk()
            ->assertSee('Análisis de tu gimnasio');
    }

    public function test_4_admin_routes_still_require_authentication(): void
    {
        $this->get('/admin')
            ->assertRedirect(route('admin.login'));
    }

    public function test_5_two_analyses_have_different_tokens(): void
    {
        $first = $this->analysis($this->lead('Fit Club', 'a@fitclub.test'));
        $second = $this->analysis($this->lead('Power Gym', 'b@fitclub.test'));

        $this->assertNotNull($first->public_token);
        $this->assertNotNull($second->public_token);
        $this->assertNotSame($first->public_token, $second->public_token);
        $this->assertSame(1, GymLeadAnalysis::query()->where('public_token', $first->public_token)->count());
    }

    public function test_6_token_is_long_random_and_not_the_id(): void
    {
        $lead = $this->lead();
        $analysis = $this->analysis($lead);

        $token = $analysis->public_token;

        $this->assertSame(64, strlen($token));
        $this->assertSame(1, preg_match('/^[A-Za-z0-9]+$/', $token));
        $this->assertSame(0, preg_match('/^\d+$/', $token), 'El token no debe ser solo números.');
        $this->assertNotSame((string) $lead->id, $token);
        $this->assertNotSame((string) $analysis->id, $token);
    }

    public function test_7_public_report_does_not_expose_member_personal_data(): void
    {
        $content = file_get_contents(base_path(self::FIXTURE_PATH));

        $this->post(route('lead.store'), [
            ...$this->validPayload(),
            'csv' => UploadedFile::fake()->createWithContent('socios_20.csv', $content),
        ]);

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();
        $analysis = $lead->analysis;

        $this->assertNotNull($analysis);

        $response = $this->get(route('analysis.public.show', $analysis->public_token))
            ->assertOk();

        $response
            ->assertDontSee('Juan García')
            ->assertDontSee('juan.garcia@example.com')
            ->assertDontSee('10001')
            ->assertDontSee('maria.sanchez@example.com')
            ->assertDontSee('ana@fitclub.test')
            ->assertDontSee('Descargar CSV');
    }

    public function test_8_csv_is_not_accessible_through_public_report(): void
    {
        $lead = $this->lead();
        $analysis = $this->analysis($lead);

        $this->get('/analisis/'.$analysis->public_token.'/csv')
            ->assertNotFound();

        $this->get(route('analysis.public.show', $analysis->public_token))
            ->assertOk()
            ->assertDontSee('/csv')
            ->assertDontSee('Descargar CSV')
            ->assertDontSee($lead->csv_path ?? 'csv/');
    }

    public function test_9_admin_can_still_view_report_and_sees_public_link(): void
    {
        $lead = $this->lead();
        $analysis = $this->analysis($lead);

        $publicUrl = route('analysis.public.show', $analysis->public_token);

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.report', $lead))
            ->assertOk()
            ->assertSee('Análisis de tu gimnasio')
            ->assertSee('Ver informe público')
            ->assertSee($publicUrl);
    }

    public function test_10_public_cta_works_and_does_not_use_test_mailto(): void
    {
        $lead = $this->lead();
        $analysis = $this->analysis($lead);

        $this->get(route('analysis.public.show', $analysis->public_token))
            ->assertOk()
            ->assertDontSee('mailto:')
            ->assertSee('Quiero mejorar mi gimnasio');

        $this->post(route('analysis.public.contact', $analysis->public_token), [
            'name' => 'Carlos Ruiz',
            'email' => 'carlos@powergym.test',
            'message' => 'Quiero recuperar a mis socios inactivos.',
        ])
            ->assertRedirect(route('analysis.public.show', $analysis->public_token));

        $this->assertDatabaseHas('gym_leads', [
            'id' => $lead->id,
            'status' => 'interested',
        ]);

        $lead->refresh();

        $this->assertStringContainsString('Contacto desde informe público', (string) $lead->notes);
        $this->assertStringContainsString('carlos@powergym.test', (string) $lead->notes);
    }

    public function test_public_report_sets_noindex_and_private_cache_headers(): void
    {
        $lead = $this->lead();
        $analysis = $this->analysis($lead);

        $response = $this->get(route('analysis.public.show', $analysis->public_token))
            ->assertOk();

        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('private', $cacheControl);
        $response->assertSee('noindex, nofollow, noarchive');
    }

    public function test_contact_endpoint_requires_valid_token(): void
    {
        $this->post(route('analysis.public.contact', 'token-que-no-existe'), [
            'name' => 'Carlos',
            'email' => 'carlos@powergym.test',
        ])->assertNotFound();
    }

    public function test_contact_endpoint_validates_input(): void
    {
        $lead = $this->lead();
        $analysis = $this->analysis($lead);

        $this->post(route('analysis.public.contact', $analysis->public_token), [
            'name' => '',
            'email' => 'no-es-un-email',
        ])->assertSessionHasErrors(['name', 'email']);

        $lead->refresh();

        $this->assertSame('new', $lead->status);
        $this->assertNull($lead->notes);
    }

    public function test_contact_endpoint_rejects_honeypot_filled(): void
    {
        $lead = $this->lead();
        $analysis = $this->analysis($lead);

        $this->post(route('analysis.public.contact', $analysis->public_token), [
            'name' => 'Bot',
            'email' => 'bot@example.test',
            'website' => 'http://spam.example',
        ])->assertSessionHasErrors('website');

        $lead->refresh();

        $this->assertSame('new', $lead->status);
        $this->assertNull($lead->notes);
    }

    public function test_admin_lead_detail_shows_public_link_and_copy_button(): void
    {
        $lead = $this->lead();
        $analysis = $this->analysis($lead);

        $publicUrl = route('analysis.public.show', $analysis->public_token);

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.show', $lead))
            ->assertOk()
            ->assertSee('Informe público')
            ->assertSee('Copiar enlace')
            ->assertSee($publicUrl);
    }

    public function test_11_token_generated_automatically_on_analysis_creation(): void
    {
        $content = file_get_contents(base_path(self::FIXTURE_PATH));

        $this->post(route('lead.store'), [
            ...$this->validPayload(),
            'csv' => UploadedFile::fake()->createWithContent('socios_20.csv', $content),
        ]);

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();
        $analysis = $lead->analysis;

        $this->assertNotNull($analysis);
        $this->assertNotNull($analysis->public_token);
        $this->assertNotSame((string) $lead->id, $analysis->public_token);
    }

    public function test_12_real_20_record_csv_produces_expected_metrics_and_public_report(): void
    {
        $content = file_get_contents(base_path(self::FIXTURE_PATH));

        $response = $this->post(route('lead.store'), [
            ...$this->validPayload(),
            'csv' => UploadedFile::fake()->createWithContent('socios_20.csv', $content),
        ]);

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();

        $response->assertRedirect($lead->analysis->publicUrl());

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

        $analysis = $lead->analysis;
        $this->assertNotNull($analysis);
        $this->assertNotNull($analysis->public_token);

        $this->get(route('analysis.public.show', $analysis->public_token))
            ->assertOk()
            ->assertSee('Análisis de tu gimnasio')
            ->assertSee('05/09/2026')
            ->assertSee('Potencial de reactivación')
            ->assertSee('833,70')
            ->assertSee('Hemos detectado 7 socios inactivos que podrían ser candidatos a una campaña de reactivación.')
            ->assertSee('Hemos detectado 3 bajas durante los últimos 90 días.')
            ->assertSee('Se analizaron 20 registros y todos eran válidos.')
            ->assertSee('Quiero mejorar mi gimnasio')
            ->assertDontSee('Juan García')
            ->assertDontSee('juan.garcia@example.com')
            ->assertDontSee('10001')
            ->assertDontSee('mailto:');
    }
}
