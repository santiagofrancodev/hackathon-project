<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\User;

class AssessmentPolicy
{
    public function viewAny(User $user, ?int $companyId = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Evaluator and User can see their own
        if ($user->isEvaluator() || $user->isUser()) {
            return true;
        }

        // Auditor can only see assigned companies
        if ($user->isAuditor()) {
            if ($companyId) {
                return $user->auditedCompanies()->where('company_id', $companyId)->exists();
            }

            return $user->auditedCompanies()->exists();
        }

        return false;
    }

    public function view(User $user, Assessment $assessment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (($user->isEvaluator() || $user->isUser()) && $assessment->user_id === $user->id) {
            return true;
        }

        if ($user->isAuditor()) {
            return $user->auditedCompanies()
                ->where('company_id', $assessment->company_id)
                ->exists();
        }

        return false;
    }

    public function create(User $user, int $companyId): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isEvaluator() || $user->isUser()) {
            return $user->companies()->where('id', $companyId)->exists();
        }

        return false;
    }

    public function update(User $user, Assessment $assessment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $assessment->user_id === $user->id;
    }
}
