<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $companies = Company::where('user_id', $user->id)->get();

        $recentAssessments = Assessment::where('user_id', $user->id)
            ->with('company')
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_assessments' => Assessment::where('user_id', $user->id)->count(),
            'completed_assessments' => Assessment::where('user_id', $user->id)->where('status', 'completed')->count(),
            'companies_count' => $companies->count(),
            'average_score' => Assessment::where('user_id', $user->id)
                ->where('status', 'completed')
                ->avg('score'),
        ];

        return view('dashboard', compact('companies', 'recentAssessments', 'stats'));
    }
}
