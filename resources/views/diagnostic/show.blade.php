<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-body-text leading-tight">
            {{ __('Cuestionario de Autodiagnóstico') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-body-text">Empresa: {{ $assessment->company->name }}</h3>
                        <p class="text-sm text-muted-text">Responda cada pregunta según corresponda. Las preguntas complementarias no afectan el puntaje.</p>
                    </div>

                    <form action="{{ route('diagnostic.submit', $assessment) }}" method="POST" id="diagnosticForm">
                        @csrf

                        @foreach($categories as $category)
                            @php
                                $questions = $category->questions;
                                // Build children map for conditional questions
                                $childrenMap = [];
                                foreach ($questions as $q) {
                                    if ($q->parent_question_id) {
                                        $childrenMap[$q->parent_question_id][] = $q;
                                    }
                                }
                            @endphp
                            <div class="mb-8">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-md font-bold text-body-text">{{ $category->name }}</h4>
                                    <span class="text-sm text-primary font-medium">Peso máximo: {{ $category->max_percentage }}%</span>
                                </div>

                                <div class="space-y-3">
                                    @foreach($questions as $question)
                                        @php
                                            // Skip child questions — rendered inside their parent
                                            if ($question->parent_question_id) { continue; }

                                            $answer = $answers->get($question->id);
                                            $hasChildren = isset($childrenMap[$question->id]) && count($childrenMap[$question->id]) > 0;
                                        @endphp

                                        @if($hasChildren)
                                            {{-- Parent question with conditional children --}}
                                            <div x-data="{ showChildren: {{ $answer && $answer->answer ? 'true' : 'false' }} }">
                                                {{-- Parent card --}}
                                                <div class="border rounded-lg p-4 border-border-light bg-card-bg">
                                                    <div class="flex items-start gap-3">
                                                        <div class="flex-1">
                                                            <p class="text-sm font-medium text-body-text">
                                                                {{ $question->question_text }}
                                                                @if($question->weight > 0)
                                                                    <span class="ml-2 text-xs bg-primary/10 text-primary px-2 py-0.5 rounded">{{ $question->weight }}%</span>
                                                                @endif
                                                            </p>
                                                            @if($question->help_text)
                                                                <p class="mt-1 text-xs text-muted-text">{{ $question->help_text }}</p>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center gap-2 shrink-0">
                                                            <label class="inline-flex items-center">
                                                                <input type="radio" name="answers[{{ $question->id }}][question_id]" value="{{ $question->id }}" checked hidden>
                                                                <input type="radio"
                                                                       name="answers[{{ $question->id }}][answer]" value="1"
                                                                       class="h-4 w-4 text-high-text border-border-light focus:ring-high-text"
                                                                       {{ $answer && $answer->answer ? 'checked' : '' }}
                                                                       x-on:change="showChildren = true">
                                                                <span class="ml-1 text-sm text-high-text">Sí</span>
                                                            </label>
                                                            <label class="inline-flex items-center">
                                                                <input type="radio"
                                                                       name="answers[{{ $question->id }}][answer]" value="0"
                                                                       class="h-4 w-4 text-low-text border-border-light focus:ring-low-text"
                                                                       {{ $answer !== null && !$answer->answer ? 'checked' : '' }}
                                                                       x-on:change="showChildren = false">
                                                                <span class="ml-1 text-sm text-low-text">No</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Child questions (shown only if parent=Sí) --}}
                                                @foreach($childrenMap[$question->id] as $child)
                                                    @php $childAnswer = $answers->get($child->id); @endphp
                                                    <div x-show="showChildren"
                                                         x-transition:enter="transition ease-out duration-200"
                                                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                                                         x-transition:enter-end="opacity-100 transform translate-y-0"
                                                         class="ml-6 mt-2 border border-dashed border-border-light rounded-lg p-4 {{ $child->is_complementary ? 'bg-bg-page' : 'bg-card-bg' }}">
                                                        <div class="flex items-start gap-3">
                                                            <div class="flex-1">
                                                                <p class="text-sm font-medium text-body-text">
                                                                    {{ $child->question_text }}
                                                                    @if($child->is_complementary)
                                                                        <span class="ml-2 text-xs bg-medium-bg text-medium-text px-2 py-0.5 rounded">Complementaria</span>
                                                                    @endif
                                                                    @if($child->weight > 0)
                                                                        <span class="ml-2 text-xs bg-primary/10 text-primary px-2 py-0.5 rounded">{{ $child->weight }}%</span>
                                                                    @endif
                                                                </p>
                                                                @if($child->help_text)
                                                                    <p class="mt-1 text-xs text-muted-text">{{ $child->help_text }}</p>
                                                                @endif
                                                            </div>
                                                            <div class="flex items-center gap-2 shrink-0">
                                                                <label class="inline-flex items-center">
                                                                    <input type="radio" name="answers[{{ $child->id }}][question_id]" value="{{ $child->id }}" checked hidden>
                                                                    <input type="radio"
                                                                           name="answers[{{ $child->id }}][answer]" value="1"
                                                                           class="h-4 w-4 text-high-text border-border-light focus:ring-high-text"
                                                                           {{ $childAnswer && $childAnswer->answer ? 'checked' : '' }}>
                                                                    <span class="ml-1 text-sm text-high-text">Sí</span>
                                                                </label>
                                                                <label class="inline-flex items-center">
                                                                    <input type="radio"
                                                                           name="answers[{{ $child->id }}][answer]" value="0"
                                                                           class="h-4 w-4 text-low-text border-border-light focus:ring-low-text"
                                                                           {{ $childAnswer !== null && !$childAnswer->answer ? 'checked' : '' }}>
                                                                    <span class="ml-1 text-sm text-low-text">No</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            {{-- Standalone question (no children) --}}
                                            <div class="border rounded-lg p-4 border-border-light bg-card-bg">
                                                <div class="flex items-start gap-3">
                                                    <div class="flex-1">
                                                        <p class="text-sm font-medium text-body-text">
                                                            {{ $question->question_text }}
                                                            @if($question->weight > 0)
                                                                <span class="ml-2 text-xs bg-primary/10 text-primary px-2 py-0.5 rounded">{{ $question->weight }}%</span>
                                                            @endif
                                                        </p>
                                                        @if($question->help_text)
                                                            <p class="mt-1 text-xs text-muted-text">{{ $question->help_text }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-2 shrink-0">
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="answers[{{ $question->id }}][question_id]" value="{{ $question->id }}" checked hidden>
                                                            <input type="radio"
                                                                   name="answers[{{ $question->id }}][answer]" value="1"
                                                                   class="h-4 w-4 text-high-text border-border-light focus:ring-high-text"
                                                                   {{ $answer && $answer->answer ? 'checked' : '' }}>
                                                            <span class="ml-1 text-sm text-high-text">Sí</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio"
                                                                   name="answers[{{ $question->id }}][answer]" value="0"
                                                                   class="h-4 w-4 text-low-text border-border-light focus:ring-low-text"
                                                                   {{ $answer !== null && !$answer->answer ? 'checked' : '' }}>
                                                            <span class="ml-1 text-sm text-low-text">No</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-8 flex items-center justify-between border-t pt-6">
                            <p class="text-sm text-muted-text">Revise bien sus respuestas antes de enviar.</p>
                            <button type="submit" class="px-8 py-3 bg-primary text-white font-semibold rounded-md hover:bg-primary-hover" onclick="return confirm('¿Está seguro de enviar el cuestionario? No podrá modificarlo después.')">
                                Enviar autodiagnóstico
                            </button>
                        </div>
                    </form>
</x-app-layout>
