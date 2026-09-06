<?php

namespace Tests\Feature;

use App\Models\GymLead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLeadManagementTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create([
            'is_admin' => true,
        ]);
    }

    private function regularUser(): User
    {
        return User::factory()->create([
            'is_admin' => false,
        ]);
    }

    private function makeLead(array $overrides = []): GymLead
    {
        return GymLead::create([
            'gym_name' => 'Fit Club',
            'contact_name' => 'Ana',
            'email' => 'ana@fitclub.test',
            'members' => 350,
            'average_fee' => 40,
            'inactive_members' => 30,
            'monthly_cancellations' => 15,
            'estimated_opportunity' => 330,
            'consent_at' => now(),
            ...$overrides,
        ]);
    }

    public function test_admin_pages_require_authentication(): void
    {
        $this->get(route('admin.leads.index'))
            ->assertRedirect(route('admin.login'));

        $lead = $this->makeLead();

        $this->get(route('admin.leads.show', $lead))
            ->assertRedirect(route('admin.login'));
    }

    public function test_regular_user_cannot_access_admin_panel(): void
    {
        $this->actingAs($this->regularUser());

        $this->get(route('admin.leads.index'))->assertForbidden();
    }

    public function test_admin_can_list_leads(): void
    {
        $this->actingAs($this->adminUser());

        $this->makeLead();

        $this->get(route('admin.leads.index'))
            ->assertOk()
            ->assertSee('Fit Club');
    }

    public function test_admin_can_see_lead_detail(): void
    {
        $this->actingAs($this->adminUser());

        $lead = $this->makeLead();

        $this->get(route('admin.leads.show', $lead))
            ->assertOk()
            ->assertSee('Fit Club')
            ->assertSee('ana@fitclub.test');
    }

    public function test_admin_can_update_lead_status(): void
    {
        $this->actingAs($this->adminUser());

        $lead = $this->makeLead();

        $this->patch(route('admin.leads.status', $lead), ['status' => 'interested'])
            ->assertRedirect();

        $this->assertDatabaseHas('gym_leads', [
            'id' => $lead->id,
            'status' => 'interested',
        ]);
    }

    public function test_admin_can_update_lead_notes(): void
    {
        $this->actingAs($this->adminUser());

        $lead = $this->makeLead();

        $this->patch(route('admin.leads.notes', $lead), ['notes' => 'Llamar el lunes.'])
            ->assertRedirect();

        $this->assertDatabaseHas('gym_leads', [
            'id' => $lead->id,
            'notes' => 'Llamar el lunes.',
        ]);
    }

    public function test_regular_user_cannot_update_lead(): void
    {
        $this->actingAs($this->regularUser());

        $lead = $this->makeLead();

        $this->patch(route('admin.leads.status', $lead), ['status' => 'won'])->assertForbidden();
        $this->patch(route('admin.leads.notes', $lead), ['notes' => 'hack'])->assertForbidden();
    }

    public function test_admin_can_filter_leads_by_status(): void
    {
        $this->actingAs($this->adminUser());

        $this->makeLead(['status' => 'new']);
        $this->makeLead(['status' => 'won', 'gym_name' => 'Power Gym', 'email' => 'x@x.test']);

        $this->get(route('admin.leads.index', ['status' => 'won']))
            ->assertOk()
            ->assertSee('Power Gym')
            ->assertDontSee('Fit Club');
    }

    public function test_new_leads_default_to_new_status(): void
    {
        $lead = $this->makeLead();

        $this->assertDatabaseHas('gym_leads', [
            'id' => $lead->id,
            'status' => 'new',
        ]);
    }

    public function test_admin_can_login_and_logout(): void
    {
        $admin = $this->adminUser();

        $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.leads.index'));

        $this->assertAuthenticated();

        $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }

    public function test_login_rejects_bad_credentials(): void
    {
        $this->post(route('admin.login.store'), [
            'email' => 'nobody@example.test',
            'password' => 'whatever',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
