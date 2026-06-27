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

                    <form action="{{ route('diagnostic.submit', $assessment) }}" method="POST" id="diagnosticForm"
                          x-data="{ aiQuestionId: null, aiLoading: false, aiText: '', aiOpen: false }">
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
                                                            <div class="flex items-start gap-2">
                                                                <p class="text-sm font-medium text-body-text">
                                                                    {{ $question->question_text }}
                                                                </p>
                                                                <button type="button"
                                                                        @@click="aiQuestionId = {{ $question->id }}; aiLoading = true; aiOpen = true; fetch('{{ route('ia.explicar') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ question_id: {{ $question->id }} }) }).then(r => r.json()).then(d => { aiText = d.explicacion; aiLoading = false; })"
                                                                        class="shrink-0 w-5 h-5 rounded-full bg-primary/10 text-primary hover:bg-primary/20 transition flex items-center justify-center"
                                                                        title="Explicar con IA">
                                                                    <span class="text-xs font-bold leading-none">?</span>
                                                                </button>
                                                            </div>
                                                            @if($question->weight > 0)
                                                                <span class="mt-1 inline-block text-xs bg-primary/10 text-primary px-2 py-0.5 rounded">{{ $question->weight }}%</span>
                                                            @endif
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
                                                        <div class="flex items-start gap-2">
                                                            <p class="text-sm font-medium text-body-text">
                                                                {{ $child->question_text }}
                                                            </p>
                                                            <button type="button"
                                                                    @@click="aiQuestionId = {{ $child->id }}; aiLoading = true; aiOpen = true; fetch('{{ route('ia.explicar') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ question_id: {{ $child->id }} }) }).then(r => r.json()).then(d => { aiText = d.explicacion; aiLoading = false; })"
                                                                    class="shrink-0 w-5 h-5 rounded-full bg-primary/10 text-primary hover:bg-primary/20 transition flex items-center justify-center"
                                                                    title="Explicar con IA">
                                                                <span class="text-xs font-bold leading-none">?</span>
                                                            </button>
                                                        </div>
                                                        @if($child->is_complementary)
                                                            <span class="mt-1 inline-block text-xs bg-medium-bg text-medium-text px-2 py-0.5 rounded">Complementaria</span>
                                                        @endif
                                                        @if($child->weight > 0)
                                                            <span class="mt-1 inline-block text-xs bg-primary/10 text-primary px-2 py-0.5 rounded">{{ $child->weight }}%</span>
                                                        @endif
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
                                                        <div class="flex items-start gap-2">
                                                            <p class="text-sm font-medium text-body-text">
                                                                {{ $question->question_text }}
                                                            </p>
                                                            <button type="button"
                                                                    @@click="aiQuestionId = {{ $question->id }}; aiLoading = true; aiOpen = true; fetch('{{ route('ia.explicar') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ question_id: {{ $question->id }} }) }).then(r => r.json()).then(d => { aiText = d.explicacion; aiLoading = false; })"
                                                                    class="shrink-0 w-5 h-5 rounded-full bg-primary/10 text-primary hover:bg-primary/20 transition flex items-center justify-center"
                                                                    title="Explicar con IA">
                                                                <span class="text-xs font-bold leading-none">?</span>
                                                            </button>
                                                        </div>
                                                        @if($question->weight > 0)
                                                            <span class="mt-1 inline-block text-xs bg-primary/10 text-primary px-2 py-0.5 rounded">{{ $question->weight }}%</span>
                                                        @endif
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

<div class="mt-8 flex flex-col sm:flex-row items-start sm:items-center justify-between border-t pt-6 gap-4"
                            x-data="{ open: false, showError: false }">
                            <div>
                                <p class="text-sm text-muted-text">Revise bien sus respuestas antes de enviar.</p>
                                <p x-show="showError" x-cloak class="mt-1 text-xs text-low-text font-medium">
                                    Debe responder al menos una pregunta antes de enviar.
                                </p>
                            </div>
                            <button type="button" @click="if (window.checkFormAnswers()) { open = true; showError = false; } else { showError = true; }" class="px-8 py-3 bg-primary text-white font-semibold rounded-md hover:bg-primary-hover transition shrink-0">
                                Enviar autodiagnóstico
                            </button>

                            {{-- Confirmation Modal --}}
                            <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;" x-cloak>
                                {{-- Backdrop --}}
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
                                {{-- Modal card --}}
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4" class="relative z-10 bg-card-bg border border-border-light rounded-2xl shadow-2xl p-6 max-w-md w-full mx-4">
                                    <div class="text-center">
                                        <div class="mx-auto w-12 h-12 bg-medium-bg rounded-xl flex items-center justify-center mb-4">
                                            <x-icon name="exclamation-triangle" class="w-6 h-6 text-medium-text" />
                                        </div>
                                        <h3 class="text-lg font-bold text-body-text mb-2">Confirmar envío</h3>
                                        <p class="text-sm text-muted-text mb-6">
                                            ¿Está seguro de enviar el cuestionario? No podrá modificarlo después.
                                        </p>
                                        <div class="flex items-center gap-3 justify-center">
                                            <button type="button" @click="open = false" class="px-6 py-2.5 border border-border-light text-body-text font-medium rounded-lg hover:bg-bg-page transition text-sm">
                                                Cancelar
                                            </button>
<button type="button" @click="document.getElementById('diagnosticForm').submit()" class="px-6 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary-hover transition text-sm">
                                                        Confirmar envío
                                                    </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- AI Explanation Modal --}}
                        <div x-show="aiOpen" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;" x-cloak>
                            <div x-show="aiOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black/50 backdrop-blur-sm" @@click="aiOpen = false"></div>
                            <div x-show="aiOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="relative z-10 bg-card-bg border border-border-light rounded-2xl shadow-2xl p-6 max-w-lg w-full mx-4">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                                        <x-icon name="bolt" class="w-5 h-5 text-primary" />
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-body-text">Explicación con IA</h3>
                                        <p class="text-xs text-muted-text">Asistente de la Ley 1581</p>
                                    </div>
                                </div>
                                <div class="min-h-[80px]">
                                    <template x-if="aiLoading">
                                        <div class="flex items-center justify-center py-8">
                                            <svg class="animate-spin h-6 w-6 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </template>
                                    <template x-if="!aiLoading && aiText">
                                        <p class="text-sm text-body-text leading-relaxed" x-text="aiText"></p>
                                    </template>
                                </div>
                                <div class="mt-6 flex justify-end">
                                    <button type="button" @@click="aiOpen = false" class="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-hover transition">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.checkFormAnswers = function() {
            const radios = document.querySelectorAll('input[type=radio][name$="[answer]"]');
            return Array.from(radios).some(function(r) { return r.checked; });
        };
    </script>
    @endpush
</x-app-layout>
