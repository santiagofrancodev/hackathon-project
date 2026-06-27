<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Companies for filter
        $companies = Company::where('user_id', $user->id)->get();

        // Base query
        $assessmentsQuery = Assessment::where('user_id', $user->id)
            ->with('company');

        // Filter by company
        if ($request->filled('company_id')) {
            $assessmentsQuery->where('company_id', $request->company_id);
        }

        // Get all assessments ordered by date
        $allAssessments = $assessmentsQuery->latest()->get();

        // Stats
        $completed = $allAssessments->where('status', 'completed');
        $stats = [
            'total_assessments' => $allAssessments->count(),
            'completed_assessments' => $completed->count(),
            'companies_count' => $companies->count(),
            'average_score' => $completed->avg('score'),
        ];

        // Score evolution (last 10 completed)
        $scoreHistory = $completed->take(10)->sortByDesc('created_at')->values();

        return view('dashboard', compact(
            'companies', 'allAssessments', 'stats', 'scoreHistory'
        ));
    }
}
