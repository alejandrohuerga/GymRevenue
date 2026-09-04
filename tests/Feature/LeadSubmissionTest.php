<?php

namespace Tests\Feature;

use App\Models\GymLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_is_stored_and_redirects_to_thanks(): void
    {
        $response = $this->post(route('lead.store'), [
            'gym_name' => 'Fit Club',
            'contact_name' => 'Ana',
            'email' => 'ana@fitclub.test',
            'software' => 'Excel',
            'members' => 350,
            'average_fee' => 40,
            'inactive_members' => 30,
            'monthly_cancellations' => 15,
            'consent' => '1',
        ]);

        $response->assertRedirect(route('thanks'));

        $lead = GymLead::where('email', 'ana@fitclub.test')->first();

        $this->assertNotNull($lead);
        $this->assertEquals(900.0, $lead->estimated_opportunity);
        $this->assertNotNull($lead->consent_at);
    }

    public function test_lead_requires_consent(): void
    {
        $response = $this->post(route('lead.store'), [
            'gym_name' => 'Fit Club',
            'contact_name' => 'Ana',
            'email' => 'ana@fitclub.test',
        ]);

        $response->assertSessionHasErrors('consent');
        $this->assertDatabaseCount('gym_leads', 0);
    }

    public function test_calculator_endpoint_returns_estimation(): void
    {
        $response = $this->postJson(route('calculator.calculate'), [
            'members' => 350,
            'average_fee' => 40,
            'inactive_members' => 30,
            'monthly_cancellations' => 15,
        ]);

        $response->assertOk()
            ->assertJsonPath('estimated_opportunity', 900);
    }
}
