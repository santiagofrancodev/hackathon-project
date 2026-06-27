<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Models\Company;
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

    public function store(StoreCompanyRequest $request)
    {
        $validated = $request->validated();

        Company::create(array_merge($validated, [
            'user_id' => Auth::id(),
        ]));

        return redirect()->route('company.index')
            ->with('success', 'Empresa registrada correctamente.');
    }
}
