<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AuditorRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $auditorRequests = collect();
        $auditors = collect();

        // ---- Admin: view all ----
        if ($user->isAdmin()) {
            $companies = Company::withCount('assessments')->get();
            $companyIds = $companies->pluck('id');

            // Pending auditor requests
            $auditorRequests = AuditorRequest::with(['company', 'assessment', 'requester'])
                ->where('status', 'pending')
                ->latest()
                ->get();

            // Available auditors for assignment
            $auditors = User::where('role', 'auditor')->get();

            $assessmentsQuery = Assessment::with(['company', 'user'])
                ->whereIn('company_id', $companyIds);

            if ($request->filled('company_id')) {
                $assessmentsQuery->where('company_id', $request->company_id);
            }

            $allAssessments = $assessmentsQuery->latest()->get();
            $completed = $allAssessments->where('status', 'completed');

            $stats = [
                'total_assessments' => Assessment::count(),
                'completed_assessments' => Assessment::where('status', 'completed')->count(),
                'companies_count' => $companies->count(),
                'average_score' => Assessment::where('status', 'completed')->avg('score'),
            ];

            // Show evaluator column for admin
            $showEvaluator = true;

            // ---- Auditor: assigned companies only ----
        } elseif ($user->isAuditor()) {
            $companies = $user->auditedCompanies;
            $companyIds = $companies->pluck('id');

            $assessmentsQuery = Assessment::with('company')
                ->whereIn('company_id', $companyIds);

            if ($request->filled('company_id')) {
                $assessmentsQuery->where('company_id', $request->company_id);
            }

            $allAssessments = $assessmentsQuery->latest()->get();
            $completed = $allAssessments->where('status', 'completed');

            $stats = [
                'total_assessments' => $allAssessments->count(),
                'completed_assessments' => $completed->count(),
                'companies_count' => $companies->count(),
                'average_score' => $completed->avg('score'),
            ];

            $showEvaluator = false;

            // ---- Evaluator: own companies only ----
        } else {
            $companies = Company::where('user_id', $user->id)->get();
            $companyIds = $companies->pluck('id');

            $assessmentsQuery = Assessment::where('user_id', $user->id)
                ->with('company');

            if ($request->filled('company_id')) {
                $assessmentsQuery->where('company_id', $request->company_id);
            }

            $allAssessments = $assessmentsQuery->latest()->get();
            $completed = $allAssessments->where('status', 'completed');

            $stats = [
                'total_assessments' => $allAssessments->count(),
                'completed_assessments' => $completed->count(),
                'companies_count' => $companies->count(),
                'average_score' => $completed->avg('score'),
            ];

            $showEvaluator = false;
        }

        $scoreHistory = $completed->take(10)->sortByDesc('created_at')->values();

        return view('dashboard', compact(
            'companies', 'allAssessments', 'stats', 'scoreHistory', 'showEvaluator',
            'auditorRequests', 'auditors'
        ));
    }
}
