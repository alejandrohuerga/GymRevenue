<?php

namespace App\Http\Controllers;

use App\Services\OpportunityEstimator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'members' => ['required', 'integer', 'min:0', 'max:100000'],
            'average_fee' => ['required', 'numeric', 'min:0', 'max:10000'],
            'inactive_members' => ['required', 'integer', 'min:0', 'max:100000'],
            'monthly_cancellations' => ['required', 'integer', 'min:0', 'max:100000'],
        ]);

        $estimatedOpportunity = OpportunityEstimator::estimate(
            (int) $validated['inactive_members'],
            (int) $validated['monthly_cancellations'],
            (float) $validated['average_fee'],
        );

        return response()->json(['estimated_opportunity' => $estimatedOpportunity]);
    }
}
