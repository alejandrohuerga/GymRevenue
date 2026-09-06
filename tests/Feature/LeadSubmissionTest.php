<?php

namespace Tests\Feature;

use App\Models\GymLead;
use App\Services\OpportunityEstimator;
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
        $this->assertEquals(330.0, $lead->estimated_opportunity);
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

    public function test_lead_requires_calculator_fields(): void
    {
        $response = $this->post(route('lead.store'), [
            'gym_name' => 'Fit Club',
            'contact_name' => 'Ana',
            'email' => 'ana@fitclub.test',
            'consent' => '1',
        ]);

        $response->assertSessionHasErrors(['members', 'average_fee', 'inactive_members', 'monthly_cancellations']);
        $this->assertDatabaseCount('gym_leads', 0);
    }

    public function test_lead_rejects_inactive_members_greater_than_members(): void
    {
        $response = $this->post(route('lead.store'), [
            'gym_name' => 'Fit Club',
            'contact_name' => 'Ana',
            'email' => 'ana@fitclub.test',
            'members' => 10,
            'average_fee' => 40,
            'inactive_members' => 20,
            'monthly_cancellations' => 5,
            'consent' => '1',
        ]);

        $response->assertSessionHasErrors('inactive_members');
        $this->assertDatabaseCount('gym_leads', 0);
    }

    public function test_lead_rejects_zero_fee(): void
    {
        $response = $this->post(route('lead.store'), [
            'gym_name' => 'Fit Club',
            'contact_name' => 'Ana',
            'email' => 'ana@fitclub.test',
            'members' => 350,
            'average_fee' => 0,
            'inactive_members' => 30,
            'monthly_cancellations' => 15,
            'consent' => '1',
        ]);

        $response->assertSessionHasErrors('average_fee');
        $this->assertDatabaseCount('gym_leads', 0);
    }

    public function test_lead_rejects_negative_values(): void
    {
        $response = $this->post(route('lead.store'), [
            'gym_name' => 'Fit Club',
            'contact_name' => 'Ana',
            'email' => 'ana@fitclub.test',
            'members' => -5,
            'average_fee' => -40,
            'inactive_members' => -30,
            'monthly_cancellations' => -15,
            'consent' => '1',
        ]);

        $response->assertSessionHasErrors(['members', 'average_fee', 'inactive_members', 'monthly_cancellations']);
        $this->assertDatabaseCount('gym_leads', 0);
    }

    public function test_lead_rejects_huge_values(): void
    {
        $response = $this->post(route('lead.store'), [
            'gym_name' => 'Fit Club',
            'contact_name' => 'Ana',
            'email' => 'ana@fitclub.test',
            'members' => 1000001,
            'average_fee' => 10001,
            'inactive_members' => 1000001,
            'monthly_cancellations' => 1000001,
            'consent' => '1',
        ]);

        $response->assertSessionHasErrors(['members', 'average_fee', 'inactive_members', 'monthly_cancellations']);
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
            ->assertJsonPath('estimated_opportunity', 330)
            ->assertJsonPath('estimated_annual_opportunity', 3960)
            ->assertJsonPath('breakdown.inactive', 240)
            ->assertJsonPath('breakdown.cancellation', 90);
    }

    public function test_calculator_endpoint_rejects_invalid_payload(): void
    {
        $response = $this->postJson(route('calculator.calculate'), [
            'members' => -1,
            'average_fee' => 0,
            'inactive_members' => 100,
            'monthly_cancellations' => 'abc',
        ]);

        $response->assertUnprocessable();
    }

    public function test_estimator_matches_documented_example(): void
    {
        $monthly = OpportunityEstimator::estimate(30, 15, 40);
        $annual = OpportunityEstimator::estimateAnnual(30, 15, 40);

        $this->assertEquals(330.0, $monthly);
        $this->assertEquals(3960.0, $annual);
    }
}
