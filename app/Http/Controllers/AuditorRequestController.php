<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignAuditorRequest;
use App\Http\Requests\StoreAuditorRequest;
use App\Models\Assessment;
use App\Models\AuditorRequest;
use App\Models\User;
use App\Notifications\AuditorAssignedToAuditorNotification;
use App\Notifications\AuditorAssignedToCompanyNotification;
use App\Notifications\NewAuditorRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditorRequestController extends Controller
{
    public function store(StoreAuditorRequest $request)
    {
        $validated = $request->validated();

        $assessment = Assessment::with('company')->findOrFail($validated['assessment_id']);

        // Only the assessment owner can request an auditor
        if ($assessment->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para solicitar un auditor para esta evaluación.');
        }

        // Check if there's already a pending request
        $existing = AuditorRequest::where('assessment_id', $assessment->id)
            ->whereIn('status', ['pending', 'assigned'])
            ->first();

        if ($existing) {
            return back()->with('info', 'Ya existe una solicitud de auditoría para esta evaluación.');
        }

        $auditorRequest = AuditorRequest::create([
            'assessment_id' => $assessment->id,
            'company_id' => $assessment->company_id,
            'requester_id' => Auth::id(),
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Notify all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewAuditorRequestNotification($auditorRequest));
        }

        return back()->with('success', 'Solicitud de auditoría enviada correctamente. Un administrador revisará tu solicitud.');
    }

    public function assign(AssignAuditorRequest $request, AuditorRequest $auditorRequest)
    {
        $validated = $request->validated();

        $auditor = User::findOrFail($validated['auditor_id']);

        if (! $auditor->isAuditor()) {
            return back()->withErrors(['auditor_id' => 'El usuario seleccionado no tiene rol de auditor.']);
        }

        // Insert into auditor_company pivot
        $auditor->auditedCompanies()->syncWithoutDetaching([$auditorRequest->company_id]);

        // Update request status
        $auditorRequest->update([
            'assigned_auditor_id' => $auditor->id,
            'status' => 'assigned',
        ]);

        // Notify the auditor
        $auditor->notify(new AuditorAssignedToAuditorNotification($auditorRequest));

        // Notify the requester
        $auditorRequest->requester->notify(new AuditorAssignedToCompanyNotification($auditorRequest));

        return back()->with('success', "Auditor {$auditor->name} asignado correctamente a {$auditorRequest->company->name}.");
    }
}
