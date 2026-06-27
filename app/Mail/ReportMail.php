<?php

namespace App\Mail;

use App\Models\Assessment;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public Assessment $assessment;

    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Informe de Diagnóstico - CheckData AI',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.report',
            with: [
                'assessment' => $this->assessment,
            ],
        );
    }

    public function attachments(): array
    {
        $pdfContent = $this->generatePdf();

        return [
            Attachment::fromData(
                fn () => $pdfContent,
                $this->filename()
            )->withMime('application/pdf'),
        ];
    }

    private function generatePdf(): string
    {
        $assessment = $this->assessment;
        $categories = Category::with(['questions' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')->get();
        $answers = $assessment->answers->keyBy('question_id');

        $categoryResults = [];
        foreach ($categories as $category) {
            $earned = 0;
            $totalWeight = 0;
            foreach ($category->questions as $question) {
                if ($question->is_complementary || $question->weight === 0) {
                    continue;
                }
                $totalWeight += $question->weight;
                $answer = $answers->get($question->id);
                if ($answer && $answer->answer) {
                    $earned += $question->weight;
                }
            }
            $categoryResults[$category->id] = [
                'name' => $category->name,
                'max_percentage' => $category->max_percentage,
                'earned_percentage' => $totalWeight > 0
                    ? round(($earned / $totalWeight) * $category->max_percentage) : 0,
            ];
        }

        $gaps = [];
        foreach ($categories as $category) {
            foreach ($category->questions as $question) {
                if ($question->is_complementary) {
                    continue;
                }
                $answer = $answers->get($question->id);
                if (! $answer || ! $answer->answer) {
                    $gaps[] = [
                        'category' => $category->name,
                        'question' => $question->question_text,
                        'help_text' => $question->help_text,
                    ];
                }
            }
        }

        $pdf = Pdf::loadView('diagnostic.report', compact('assessment', 'categoryResults', 'gaps'));

        return $pdf->output();
    }

    private function filename(): string
    {
        $name = Str::slug($this->assessment->company->name);

        return "diagnostico-{$name}-{$this->assessment->id}.pdf";
    }
}
