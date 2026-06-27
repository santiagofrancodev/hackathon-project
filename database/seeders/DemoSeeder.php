<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Assessment;
use App\Models\Company;
use App\Models\Question;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // ===== USERS =====
        $admin = User::create([
            'name' => 'Admin CheckData',
            'email' => 'admin@checkdata.ai',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $evaluator = User::create([
            'name' => 'Carlos Evaluador',
            'email' => 'evaluador@checkdata.ai',
            'password' => Hash::make('password'),
            'role' => 'evaluator',
            'email_verified_at' => now(),
        ]);

        $auditor = User::create([
            'name' => 'María Auditora',
            'email' => 'auditor@checkdata.ai',
            'password' => Hash::make('password'),
            'role' => 'auditor',
            'email_verified_at' => now(),
        ]);

        $freeUser = User::create([
            'name' => 'Pedro Cliente Free',
            'email' => 'free@checkdata.ai',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // ===== COMPANIES =====
        $company1 = Company::create([
            'user_id' => $evaluator->id,
            'name' => 'TechSolutions SAS',
            'nit' => '900.123.456-7',
            'sector' => 'Tecnología',
            'size' => 'medium',
            'plan' => 'pro',
        ]);

        $company2 = Company::create([
            'user_id' => $evaluator->id,
            'name' => 'Clínica SaludTotal',
            'nit' => '800.234.567-8',
            'sector' => 'Salud',
            'size' => 'large',
            'plan' => 'pro',
        ]);

        Company::create([
            'user_id' => $evaluator->id,
            'name' => 'Distribuidora El Norte',
            'nit' => '700.345.678-9',
            'sector' => 'Comercio',
            'size' => 'small',
            'plan' => 'free',
        ]);

        // Free user with a free-plan company
        $freeCompany = Company::create([
            'user_id' => $freeUser->id,
            'name' => 'Mi Emprendimiento',
            'nit' => '600.456.789-0',
            'sector' => 'Servicios',
            'size' => 'small',
            'plan' => 'free',
        ]);

        // Assign auditor to companies
        $auditor->auditedCompanies()->attach([$company1->id, $company2->id]);

        // ===== QUESTIONS for reference =====
        $questions = Question::where('is_complementary', false)
            ->where('weight', '>', 0)
            ->orderBy('id')
            ->get();

        $qIds = $questions->pluck('id', 'id');

        // ===== HISTORICAL ASSESSMENTS =====

        // Assessment 1: High score (85%) — recent
        $this->createAssessment($evaluator, $company1, now()->subDays(2), [
            2 => true, 3 => true, 4 => true, 5 => true,
            6 => true, 7 => true, 8 => true,
            9 => true, 10 => true,
        ], 85);

        // Assessment 2: Medium score (58%) — company 2
        $this->createAssessment($evaluator, $company2, now()->subDays(5), [
            2 => true, 3 => true, 4 => false, 5 => true,
            6 => true, 7 => false, 8 => true,
            9 => false, 10 => true,
        ], 58);

        // Assessment 3: Low score (32%) — company 1
        $this->createAssessment($evaluator, $company1, now()->subDays(10), [
            2 => true, 3 => false, 4 => false, 5 => false,
            6 => true, 7 => false, 8 => true,
            9 => false, 10 => false,
        ], 32);

        // Assessment 4: High score (92%) — company 2
        $this->createAssessment($evaluator, $company2, now()->subDays(15), [
            2 => true, 3 => true, 4 => true, 5 => true,
            6 => true, 7 => true, 8 => true,
            9 => true, 10 => true,
        ], 92);

        // Assessment 5: Medium score (46%) — company 1
        $this->createAssessment($evaluator, $company1, now()->subDays(22), [
            2 => true, 3 => true, 4 => false, 5 => false,
            6 => false, 7 => true, 8 => false,
            9 => true, 10 => true,
        ], 46);

        // Assessment 6: Low score (22%) — company 2 (oldest)
        $this->createAssessment($evaluator, $company2, now()->subDays(35), [
            2 => false, 3 => false, 4 => false, 5 => false,
            6 => true, 7 => false, 8 => false,
            9 => true, 10 => false,
        ], 22);

        // Assessment 7: Free user — one assessment for the free company
        $this->createAssessment($freeUser, $freeCompany, now()->subDays(3), [
            2 => true, 3 => false, 4 => false, 5 => true,
            6 => false, 7 => true, 8 => false,
            9 => false, 10 => false,
        ], 26);
    }

    private function createAssessment(User $user, Company $company, $date, array $answers, int $score): void
    {
        $assessment = Assessment::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'status' => 'completed',
            'score' => $score,
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        // Create answers
        foreach ($answers as $questionId => $value) {
            Answer::create([
                'assessment_id' => $assessment->id,
                'question_id' => $questionId,
                'answer' => $value,
                'notes' => null,
            ]);
        }

        // Generate recommendations for gaps
        $recommendationTexts = config('recommendations.by_question', []);
        foreach ($answers as $questionId => $value) {
            if ($value) {
                continue;
            }

            $question = Question::find($questionId);
            if (! $question) {
                continue;
            }

            $priority = match (true) {
                $question->weight >= 12 => 'high',
                $question->weight >= 8 => 'medium',
                default => 'low',
            };

            $text = $recommendationTexts[$questionId] ?? sprintf(
                'Revise e implemente medidas para abordar: %s',
                $question->question_text
            );

            Recommendation::create([
                'assessment_id' => $assessment->id,
                'question_id' => $questionId,
                'text' => $text,
                'priority' => $priority,
                'origin' => 'rule',
            ]);
        }
    }
}
