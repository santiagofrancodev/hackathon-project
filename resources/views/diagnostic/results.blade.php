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
            <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg mb-6">
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
                </div>
            </div>

            {{-- AI Executive Report --}}
            @if($assessment->company->isPro())
                <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg mb-6"
                     x-data="{
                         loading: false,
                         summary: {{ $assessment->ai_summary ? json_encode($assessment->ai_summary) : 'null' }},
                         generate() {
                             this.loading = true;
                             fetch('{{ route('ia.informe') }}', {
                                 method: 'POST',
                                 headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                 body: JSON.stringify({ assessment_id: {{ $assessment->id }} })
                             })
                             .then(r => r.json())
                             .then(d => { this.summary = d.summary; this.loading = false; })
                             .catch(() => { this.loading = false; });
                         }
                     }">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-9 h-9 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                                <x-icon name="bolt" class="w-4 h-4 text-primary" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-body-text">Informe Ejecutivo con IA</h3>
                                <p class="text-xs text-muted-text">Análisis experto basado en Ley 1581 de Colombia</p>
                            </div>
                            <span class="ml-auto shrink-0 inline-flex items-center px-2 py-0.5 text-xs font-semibold bg-primary/10 text-primary rounded-full">Pro</span>
                        </div>

                        {{-- Report loaded --}}
                        <template x-if="summary && !loading">
                            <div class="text-sm text-body-text leading-relaxed" x-html="
                                summary
                                    .replace(/## (.+)/g, '<h4 class=\'text-sm font-bold text-primary mt-5 mb-2 first:mt-0\'>$1</h4>')
                                    .replace(/=== (.+?) ===/g, '<h4 class=\'text-sm font-bold text-primary mt-5 mb-2 first:mt-0\'>$1</h4>')
                                    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                                    .replace(/•/g, '&bull;')
                                    .replace(/\n/g, '<br>')
                            "></div>
                        </template>

                        {{-- Loading --}}
                        <template x-if="loading">
                            <div class="flex items-center gap-3 py-8 text-muted-text text-sm">
                                <svg class="animate-spin h-5 w-5 text-primary shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Generando informe ejecutivo con IA experta en Ley 1581...
                            </div>
                        </template>

                        {{-- Not yet generated --}}
                        <template x-if="!summary && !loading">
                            <div class="text-center py-6">
                                <p class="text-sm text-muted-text mb-4">Analiza tus resultados con un consultor IA experto en Ley 1581. Recibirás diagnóstico, riesgos legales concretos y una hoja de ruta 30/60/90 días.</p>
                                <button type="button" @@click="generate()"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-hover transition">
                                    <x-icon name="bolt" class="w-4 h-4" />
                                    Generar informe ejecutivo
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            @else
                <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg mb-6">
                    <div class="p-5 flex items-center gap-4">
                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                            <x-icon name="bolt" class="w-5 h-5 text-primary" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-body-text">Informe Ejecutivo con IA</p>
                            <p class="text-xs text-muted-text mt-0.5">Diagnóstico experto, riesgos legales ante la SIC y hoja de ruta personalizada. Disponible en plan Pro.</p>
                        </div>
                        <span class="shrink-0 px-3 py-1.5 text-xs font-semibold bg-primary/10 text-primary rounded-lg">Pro</span>
                    </div>
                </div>
            @endif

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

            {{-- Auditor Request CTA --}}
            @if($assessment->score < 70 || $assessment->company->isPro())
                <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg mb-6"
                     x-data="{ showForm: false, notes: '' }">
                    <div class="p-6">
                        @php $existingRequest = $assessment->auditorRequest; @endphp
                        @if($existingRequest && $existingRequest->status === 'assigned')
                            <div class="flex items-center gap-4 p-4 bg-high-bg border border-high-text/30 rounded-lg">
                                <div class="w-10 h-10 bg-high-text/10 rounded-full flex items-center justify-center shrink-0">
                                    <x-icon name="user-check" class="w-5 h-5 text-high-text" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-body-text">Auditor asignado</p>
                                    <p class="text-sm text-muted-text">{{ $existingRequest->assignedAuditor?->name ?? 'Pendiente' }}</p>
                                </div>
                            </div>
                        @elseif($existingRequest && $existingRequest->status === 'pending')
                            <div class="flex items-center gap-4 p-4 bg-medium-bg border border-medium-text/30 rounded-lg">
                                <div class="w-10 h-10 bg-medium-text/10 rounded-full flex items-center justify-center shrink-0">
                                    <x-icon name="clock" class="w-5 h-5 text-medium-text" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-body-text">Solicitud enviada</p>
                                    <p class="text-sm text-muted-text">Estamos revisando tu solicitud. Pronto te asignaremos un auditor.</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                                    <x-icon name="user-group" class="w-5 h-5 text-primary" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-body-text">¿Necesitás ayuda para implementar estas mejoras?</p>
                                    <p class="text-xs text-muted-text mt-1">Un auditor especializado en Ley 1581 puede guiarte en el proceso de adecuación. Te asignaremos un profesional certificado.</p>

                                    <div x-show="!showForm" class="mt-3">
                                        <button type="button" @@click="showForm = true"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-hover transition">
                                            <x-icon name="user-plus" class="w-4 h-4" />
                                            Solicitar Asignación de Auditor
                                        </button>
                                    </div>

                                    <div x-show="showForm" class="mt-3" x-cloak>
                                        <form action="{{ route('auditor-request.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="assessment_id" value="{{ $assessment->id }}">
                                            <textarea name="notes" x-model="notes" rows="2"
                                                      class="w-full text-sm border-border-light rounded-lg resize-none focus:border-primary focus:ring-primary mb-3"
                                                      placeholder="Contanos brevemente qué aspectos necesitás reforzar... (opcional)"></textarea>
                                            <div class="flex items-center gap-2">
                                                <button type="submit"
                                                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-hover transition">
                                                    <x-icon name="paper-airplane" class="w-4 h-4" />
                                                    Enviar solicitud
                                                </button>
                                                <button type="button" @@click="showForm = false"
                                                        class="px-3 py-2 text-sm text-muted-text hover:text-body-text transition">
                                                    Cancelar
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <a href="{{ route('dashboard') }}" class="text-sm text-primary hover:text-primary-hover">&larr; Volver al dashboard</a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('diagnostic.pdf', $assessment) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 border border-border-light text-body-text text-sm font-medium rounded-lg hover:bg-bg-page transition">
                        <x-icon name="clipboard-document" class="w-4 h-4" />
                        Descargar PDF
                    </a>
                    <form action="{{ route('diagnostic.email', $assessment) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 border border-border-light text-body-text text-sm font-medium rounded-lg hover:bg-bg-page transition">
                            <x-icon name="envelope" class="w-4 h-4" />
                            Enviar por email
                        </button>
                    </form>
                    <a href="{{ route('diagnostic.index') }}" class="px-6 py-2 bg-primary text-white font-semibold rounded-md hover:bg-primary-hover">
                        Nuevo autodiagnóstico
                    </a>
                </div>
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
