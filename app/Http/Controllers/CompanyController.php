<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::where('user_id', Auth::id())->withCount('assessments')->get();

        return view('company.index', compact('companies'));
    }

    public function create()
    {
        return view('company.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nit' => 'required|string|max:50|unique:companies',
            'sector' => 'nullable|string|max:255',
            'size' => 'nullable|in:small,medium,large',
        ]);

        Company::create(array_merge($validated, [
            'user_id' => Auth::id(),
        ]));

        return redirect()->route('company.index')
            ->with('success', 'Empresa registrada correctamente.');
    }
}
