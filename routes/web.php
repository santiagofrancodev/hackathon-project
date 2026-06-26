<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosticController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Company
    Route::get('/companies', [CompanyController::class, 'index'])->name('company.index');
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('company.create');
    Route::post('/companies', [CompanyController::class, 'store'])->name('company.store');

    // Diagnostic (autodiagnóstico)
    Route::get('/diagnostic', [DiagnosticController::class, 'index'])->name('diagnostic.index');
    Route::post('/diagnostic', [DiagnosticController::class, 'store'])->name('diagnostic.store');
    Route::get('/diagnostic/{assessment}', [DiagnosticController::class, 'show'])->name('diagnostic.show');
    Route::post('/diagnostic/{assessment}', [DiagnosticController::class, 'submit'])->name('diagnostic.submit');
    Route::get('/diagnostic/{assessment}/results', [DiagnosticController::class, 'results'])->name('diagnostic.results');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
