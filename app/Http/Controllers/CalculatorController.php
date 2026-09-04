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
            'members' => ['required', 'integer', 'min:1', 'max:100000'],
            'average_fee' => ['required', 'numeric', 'gt:0', 'max:10000'],
            'inactive_members' => ['required', 'integer', 'min:0', 'max:100000', 'lte:members'],
            'monthly_cancellations' => ['required', 'integer', 'min:0', 'max:100000'],
        ]);

        $inactiveMembers = (int) $validated['inactive_members'];
        $monthlyCancellations = (int) $validated['monthly_cancellations'];
        $averageFee = (float) $validated['average_fee'];

        return response()->json([
            'estimated_opportunity' => OpportunityEstimator::estimate($inactiveMembers, $monthlyCancellations, $averageFee),
            'estimated_annual_opportunity' => OpportunityEstimator::estimateAnnual($inactiveMembers, $monthlyCancellations, $averageFee),
            'breakdown' => OpportunityEstimator::breakdown($inactiveMembers, $monthlyCancellations, $averageFee),
        ]);
    }
}
