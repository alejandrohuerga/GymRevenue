<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Models\GymLead;
use App\Services\OpportunityEstimator;
use Illuminate\Http\RedirectResponse;

class LeadController extends Controller
{
    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $validated = $request->safe()->except(['consent', 'website']);

        $estimatedOpportunity = OpportunityEstimator::estimate(
            (int) ($validated['inactive_members'] ?? 0),
            (int) ($validated['monthly_cancellations'] ?? 0),
            (float) ($validated['average_fee'] ?? 0),
        );

        GymLead::create([
            ...$validated,
            'estimated_opportunity' => $estimatedOpportunity,
            'consent_at' => now(),
        ]);

        return redirect()->route('thanks');
    }
}
