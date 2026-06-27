<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-body-text leading-tight">
            {{ __('Resultados del Autodiagnóstico') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-high-bg border border-high-text/30 text-high-text rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Score Gauge -->
            <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg mb-6"
                 x-data="{ aiLoading: false, aiText: '', aiOpen: false }">
                <div class="p-6 text-center">
                    <h3 class="text-lg font-semibold text-body-text mb-2">Nivel de Cumplimiento</h3>
                    <p class="text-sm text-muted-text mb-4">Empresa: <strong>{{ $assessment->company->name }}</strong></p>

                    <div class="relative inline-flex items-center justify-center">
                        <svg class="w-48 h-48 transform -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="54" fill="none" stroke="#e5e7eb" stroke-width="8"/>
                            <circle cx="60" cy="60" r="54" fill="none" 
                                stroke="{{ $assessment->score >= 70 ? '#22c55e' : ($assessment->score >= 40 ? '#eab308' : '#ef4444') }}" 
                                stroke-width="8" 
                                stroke-dasharray="339.292" 
                                stroke-dashoffset="{{ 339.292 - (339.292 * $assessment->score / 100) }}"
                                stroke-linecap="round"
                                class="transition-all duration-1000"/>
                        </svg>
                        <div class="absolute text-center">
                            <span class="text-4xl font-bold {{ $assessment->score >= 70 ? 'text-high-text' : ($assessment->score >= 40 ? 'text-medium-text' : 'text-low-text') }}">
                                {{ $assessment->score }}%
                            </span>
                            <p class="text-xs text-muted-text mt-1">
                                @if($assessment->score >= 80)
                                    Cumplimiento Alto
                                @elseif($assessment->score >= 60)
                                    Cumplimiento Moderado
                                @elseif($assessment->score >= 40)
                                    Cumplimiento Bajo
                                @else
                                    Cumplimiento Crítico
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- AI Interpretation --}}
                    <div class="mt-6 pt-4 border-t border-border-light">
                        <button type="button"
                                @@click="aiLoading = true; aiOpen = true; fetch('{{ route('ia.interpretar') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ assessment_id: {{ $assessment->id }} }) }).then(r => r.json()).then(d => { aiText = d.interpretacion; aiLoading = false; })"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary text-sm font-medium rounded-lg hover:bg-primary/20 transition">
                            <x-icon name="bolt" class="w-4 h-4" />
                            Interpretar resultados con IA
                        </button>

                        {{-- AI Modal --}}
                        <div x-show="aiOpen" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;" x-cloak>
                            <div x-show="aiOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black/50 backdrop-blur-sm" @@click="aiOpen = false"></div>
                            <div x-show="aiOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="relative z-10 bg-card-bg border border-border-light rounded-2xl shadow-2xl p-6 max-w-lg w-full mx-4">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                                        <x-icon name="bolt" class="w-5 h-5 text-primary" />
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-body-text">Interpretación con IA</h3>
                                        <p class="text-xs text-muted-text">Análisis del resultado de autodiagnóstico</p>
                                    </div>
                                </div>
                                <div class="min-h-[60px]">
                                    <template x-if="aiLoading">
                                        <div class="flex items-center justify-center py-6">
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
                    </div>
                </div>
            </div>

            <!-- Category Breakdown -->
            <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-body-text mb-4">Desglose por Bloque</h3>
                    <div class="space-y-4">
                        @foreach($categoryResults as $result)
                            @php
                                $pct = $result['earned_percentage'];
                                $total = $result['max_percentage'];
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-body-text">{{ $result['name'] }}</span>
                                    <span class="text-sm text-muted-text">{{ $pct }}% / {{ $total }}%</span>
                                </div>
                                <div class="w-full bg-border-light rounded-full h-2.5">
                                    @php $barWidth = min(100, ($pct / max($total, 1)) * 100); @endphp
                                    <div class="h-2.5 rounded-full {{ $pct >= ($total * 0.7) ? 'bg-high-text' : ($pct >= ($total * 0.4) ? 'bg-medium-text' : 'bg-low-text') }} progress-bar" 
                                         data-width="{{ $barWidth }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Gaps & Recommendations -->
            <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-body-text mb-4">
                        Brechas Identificadas
                        <span class="text-sm font-normal text-muted-text ml-2">({{ count($gaps) }} áreas de mejora)</span>
                    </h3>

                    @if(count($gaps) > 0)
                        <div class="space-y-4">
                            @foreach($gaps as $gap)
                                <div class="border-l-4 border-medium-text bg-medium-bg p-4 rounded-r-lg">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <x-icon name="check-circle" class="h-5 w-5 text-medium-text" />
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-body-text">{{ $gap['category'] }}</p>
                                            <p class="mt-1 text-sm text-muted-text">{{ $gap['question'] }}</p>
                                            @if($gap['help_text'])
                                                <p class="mt-1 text-xs text-muted-text/70">{{ $gap['help_text'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-icon name="check-circle" class="mx-auto h-12 w-12 text-high-text" />
                            <p class="mt-2 text-sm text-muted-text">¡No se identificaron brechas significativas!</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Plan de Acción / Recommendations --}}
            @if($assessment->relationLoaded('recommendations') && $assessment->recommendations->isNotEmpty())
                <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-body-text mb-4">Plan de Acción Recomendado</h3>
                        <div class="space-y-3">
                            @php
                                $sorted = $assessment->recommendations->sortBy(fn($r) => match($r->priority) { 'high' => 0, 'medium' => 1, 'low' => 2 });
                            @endphp
                            @foreach($sorted as $rec)
                                <div class="flex items-start gap-3 p-3 border border-border-light rounded-lg">
                                    <span class="shrink-0 px-2 py-1 text-xs font-bold rounded
                                        {{ $rec->priority === 'high' ? 'bg-low-bg text-low-text' :
                                           ($rec->priority === 'medium' ? 'bg-medium-bg text-medium-text' : 'bg-high-bg text-high-text') }}">
                                        {{ $rec->priority === 'high' ? 'Alta' : ($rec->priority === 'medium' ? 'Media' : 'Baja') }}
                                    </span>
                                    <div class="flex-1">
                                        <p class="text-sm text-body-text">{{ $rec->text }}</p>
                                        @if($rec->origin === 'ai')
                                            <span class="mt-1 inline-flex items-center px-1.5 py-0.5 text-xs font-medium bg-primary/10 text-primary rounded">IA</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="text-sm text-primary hover:text-primary-hover">&larr; Volver al dashboard</a>
                <a href="{{ route('diagnostic.index') }}" class="px-6 py-2 bg-primary text-white font-semibold rounded-md hover:bg-primary-hover">
                    Nuevo autodiagnóstico
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.progress-bar').forEach(function(el) {
                var width = el.getAttribute('data-width');
                if (width) {
                    el.style.width = width + '%';
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
