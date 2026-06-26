<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\Category;
use App\Models\User;
use App\Models\Company;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiagnosticTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed questions
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_assessment_completes_with_partial_answers(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Test Company',
            'nit' => '123456',
            'sector' => 'tech',
            'size' => 'medium',
        ]);

        $assessment = Assessment::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'status' => 'in_progress',
        ]);

        // Submit only some answers (simulating form without hidden conditional questions)
        $answerableQuestions = Question::where('is_complementary', false)
            ->where('weight', '>', 0)
            ->take(3)
            ->get();

        $answers = $answerableQuestions->map(fn($q) => [
            'question_id' => $q->id,
            'answer' => true,
        ])->toArray();

        $this->actingAs($user)
            ->post(route('diagnostic.submit', $assessment), ['answers' => $answers])
            ->assertRedirect(route('diagnostic.results', $assessment));

        $assessment->refresh();
        
        $this->assertEquals('completed', $assessment->status);
        $this->assertNotNull($assessment->score);
        $this->assertGreaterThanOrEqual(0, $assessment->score);
        $this->assertLessThanOrEqual(100, $assessment->score);
    }

    public function test_assessment_score_is_calculated_correctly(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Test Company',
            'nit' => '1234567',
            'sector' => 'tech',
            'size' => 'medium',
        ]);

        $assessment = Assessment::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'status' => 'in_progress',
        ]);

        // Get all scorable questions
        $questions = Question::where('is_complementary', false)
            ->where('weight', '>', 0)
            ->get();

        $answers = $questions->map(fn($q) => [
            'question_id' => $q->id,
            'answer' => true,
        ])->toArray();

        $this->actingAs($user)
            ->post(route('diagnostic.submit', $assessment), ['answers' => $answers]);

        $assessment->refresh();
        
        // All correct answers should give 100%
        $this->assertEquals(100, $assessment->score);
    }

    public function test_results_page_shows_gaps(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Test Company',
            'nit' => '1234568',
            'sector' => 'tech',
            'size' => 'medium',
        ]);

        $assessment = Assessment::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'status' => 'completed',
            'score' => 50,
        ]);

        // Submit only Q2 as "Yes" (10% weight)
        $q2 = Question::where('weight', 10)->first();
        $assessment->answers()->create([
            'question_id' => $q2->id,
            'answer' => true,
        ]);

        $this->actingAs($user)
            ->get(route('diagnostic.results', $assessment))
            ->assertOk()
            ->assertSee('12') // gaps count
            ->assertSee('Gobernanza'); // some gap
    }
}