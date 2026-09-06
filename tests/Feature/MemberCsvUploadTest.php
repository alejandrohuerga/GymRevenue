<?php

namespace Tests\Feature;

use App\Models\GymLead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class MemberCsvUploadTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_CSV = <<<'CSV'
member_id,name,email,status,join_date,last_visit,monthly_fee,cancel_date
12345,Juan García,juan@email.com,active,2025-03-15,2026-08-20,39.90,
12346,Ana López,ana@email.com,inactive,2024-10-10,2026-06-15,40.00,
12347,Carlos Martín,carlos@email.com,cancelled,2023-05-20,2026-07-01,35.00,2026-08-01
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

    private function postWithCsv(array $payload): TestResponse
    {
        return $this->post(route('lead.store'), [
            ...$this->validPayload(),
            ...$payload,
        ]);
    }

    private function postWithCsvContent(string $filename, string $content): TestResponse
    {
        return $this->postWithCsv([
            'csv' => UploadedFile::fake()->createWithContent($filename, $content),
        ]);
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function leadWithCsv(string $path = 'csv/muestra.csv', string $name = 'socios.csv'): GymLead
    {
        Storage::disk('local')->put($path, self::VALID_CSV);

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
            'csv_original_name' => $name,
            'csv_uploaded_at' => now(),
            'consent_at' => now(),
        ]);
    }

    public function test_lead_without_csv_works_as_before(): void
    {
        $response = $this->postWithCsv([]);

        $response->assertRedirect(route('thanks'));

        $this->assertDatabaseHas('gym_leads', [
            'email' => 'ana@fitclub.test',
            'csv_path' => null,
        ]);

        Storage::disk('local')->assertDirectoryEmpty('csv');
    }

    public function test_lead_with_valid_csv_is_stored_and_linked(): void
    {
        $response = $this->postWithCsvContent('socios.csv', self::VALID_CSV);

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();

        $response->assertRedirect($lead->analysis->publicUrl());

        $this->assertNotNull($lead->csv_path);
        $this->assertSame('socios.csv', $lead->csv_original_name);
        $this->assertNotNull($lead->csv_uploaded_at);
        $this->assertTrue(Storage::disk('local')->exists($lead->csv_path));
        $this->assertSame(330.0, (float) $lead->estimated_opportunity);
        $this->assertNotNull($lead->consent_at);
    }

    public function test_empty_csv_is_rejected(): void
    {
        $response = $this->postWithCsvContent('vacio.csv', '');

        $response->assertSessionHasErrors('csv');
        $this->assertDatabaseCount('gym_leads', 0);
        Storage::disk('local')->assertDirectoryEmpty('csv');
    }

    public function test_oversized_csv_is_rejected(): void
    {
        $response = $this->postWithCsv([
            'csv' => UploadedFile::fake()->create('grande.csv', 3000),
        ]);

        $response->assertSessionHasErrors('csv');
        $this->assertDatabaseCount('gym_leads', 0);
        Storage::disk('local')->assertDirectoryEmpty('csv');
    }

    public function test_non_csv_file_is_rejected(): void
    {
        $response = $this->postWithCsv([
            'csv' => UploadedFile::fake()->image('socios.png'),
        ]);

        $response->assertSessionHasErrors('csv');
        $this->assertDatabaseCount('gym_leads', 0);
        Storage::disk('local')->assertDirectoryEmpty('csv');
    }

    public function test_malformed_csv_is_rejected_and_preserves_input(): void
    {
        $content = "member_id,name,status,join_date,last_visit\n1,Juan García,active,2025-03-15,2026-08-20\n2,Incompleto\n";

        $response = $this->postWithCsvContent('roto.csv', $content);

        $response->assertSessionHasErrors('csv');
        $response->assertSessionHasInput('gym_name', 'Fit Club');
        $this->assertDatabaseCount('gym_leads', 0);
        Storage::disk('local')->assertDirectoryEmpty('csv');
    }

    public function test_csv_missing_required_columns_is_rejected(): void
    {
        $content = "member_id,name,email\n1,Juan García,juan@email.com\n";

        $response = $this->postWithCsvContent('incompleto.csv', $content);

        $response->assertSessionHasErrors('csv');

        $message = session('errors')->first('csv');

        $this->assertStringContainsString('status', $message);
        $this->assertStringContainsString('join_date', $message);
        $this->assertStringContainsString('last_visit', $message);
        $this->assertDatabaseCount('gym_leads', 0);
        Storage::disk('local')->assertDirectoryEmpty('csv');
    }

    public function test_csv_with_only_required_columns_is_accepted(): void
    {
        $content = <<<'CSV'
member_id,name,status,join_date,last_visit
1,Juan García,active,2025-03-15,2026-08-20
CSV;

        $response = $this->postWithCsvContent('minimo.csv', $content);

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();

        $response->assertRedirect($lead->analysis->publicUrl());

        $this->assertNotNull($lead->csv_path);
    }

    public function test_csv_with_spanish_characters_is_stored(): void
    {
        $content = <<<'CSV'
member_id,name,status,join_date,last_visit
1,Muñoz Sánchez,active,2025-03-15,2026-08-20
2,Óscar Pérez,inactive,2024-01-10,2026-05-01
CSV;

        $response = $this->postWithCsvContent('espanol.csv', $content);

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();

        $response->assertRedirect($lead->analysis->publicUrl());

        $this->assertNotNull($lead);
        $this->assertStringContainsString('Muñoz Sánchez', Storage::disk('local')->get($lead->csv_path));
    }

    public function test_csv_with_comma_delimiter_is_accepted(): void
    {
        $content = "member_id,name,status,join_date,last_visit\n1,Juan García,active,2025-03-15,2026-08-20\n";

        $response = $this->postWithCsvContent('comas.csv', $content);

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();

        $response->assertRedirect($lead->analysis->publicUrl());
        $this->assertDatabaseCount('gym_leads', 1);
    }

    public function test_csv_with_semicolon_delimiter_is_accepted(): void
    {
        $content = "member_id;name;status;join_date;last_visit\n1;Juan García;active;2025-03-15;2026-08-20\n2;Ana López;inactive;2024-10-10;2026-06-15\n";

        $response = $this->postWithCsvContent('puntoycoma.csv', $content);

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();

        $response->assertRedirect($lead->analysis->publicUrl());
        $this->assertDatabaseCount('gym_leads', 1);

        $lead = GymLead::where('email', 'ana@fitclub.test')->first();
        $this->assertStringContainsString('Ana López', Storage::disk('local')->get($lead->csv_path));
    }

    public function test_csv_with_utf8_bom_is_accepted(): void
    {
        $content = "\xEF\xBB\xBFmember_id,name,status,join_date,last_visit\n1,Juan García,active,2025-03-15,2026-08-20\n";

        $response = $this->postWithCsvContent('bom.csv', $content);

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();

        $response->assertRedirect($lead->analysis->publicUrl());
        $this->assertDatabaseCount('gym_leads', 1);
    }

    public function test_csv_with_txt_extension_is_accepted(): void
    {
        $response = $this->postWithCsvContent('exportacion.txt', self::VALID_CSV);

        $lead = GymLead::where('email', 'ana@fitclub.test')->firstOrFail();

        $response->assertRedirect($lead->analysis->publicUrl());
        $this->assertDatabaseCount('gym_leads', 1);
    }

    public function test_csv_is_not_publicly_accessible(): void
    {
        $lead = $this->leadWithCsv('csv/secreta.csv');

        $response = $this->get('/storage/'.$lead->csv_path);

        $response->assertStatus(403);
    }

    public function test_admin_can_see_csv_in_lead_detail(): void
    {
        $lead = $this->leadWithCsv(name: 'clientes_export.csv');

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.show', $lead))
            ->assertOk()
            ->assertSee('CSV de socios')
            ->assertSee('clientes_export.csv')
            ->assertSee('Descargar CSV');
    }

    public function test_admin_can_download_csv(): void
    {
        $lead = $this->leadWithCsv(name: 'socios.csv');

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.csv', $lead))
            ->assertOk()
            ->assertDownload('socios.csv')
            ->assertStreamedContent(self::VALID_CSV);
    }

    public function test_csv_download_requires_authentication(): void
    {
        $lead = $this->leadWithCsv();

        $response = $this->get(route('admin.leads.csv', $lead));

        $response->assertRedirect(route('admin.login'));
        $response->assertDontSee(self::VALID_CSV);
        $this->assertTrue(! $response->isOk());
    }

    public function test_csv_download_forbidden_for_regular_user(): void
    {
        $lead = $this->leadWithCsv();

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $this->get(route('admin.leads.csv', $lead))->assertForbidden();
    }

    public function test_lead_shows_no_csv_in_admin_detail_when_missing(): void
    {
        $lead = $this->leadWithCsv();

        $lead->update(['csv_path' => null, 'csv_original_name' => null, 'csv_uploaded_at' => null]);

        $this->actingAs($this->admin());

        $this->get(route('admin.leads.show', $lead))
            ->assertOk()
            ->assertSee('CSV de socios');
    }
}
