<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-body-text leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-high-bg border border-high-text/30 text-high-text rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                            <x-icon name="building-office" class="w-5 h-5 text-primary" />
                        </div>
                        <div>
                            <p class="text-xs text-muted-text font-medium">Empresas</p>
                            <p class="text-2xl font-bold text-body-text">{{ $stats['companies_count'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                            <x-icon name="clipboard-document" class="w-5 h-5 text-primary" />
                        </div>
                        <div>
                            <p class="text-xs text-muted-text font-medium">Totales</p>
                            <p class="text-2xl font-bold text-body-text">{{ $stats['total_assessments'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-high-bg rounded-lg flex items-center justify-center shrink-0">
                            <x-icon name="check-circle" class="w-5 h-5 text-high-text" />
                        </div>
                        <div>
                            <p class="text-xs text-muted-text font-medium">Completados</p>
                            <p class="text-2xl font-bold text-body-text">{{ $stats['completed_assessments'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-high-bg rounded-lg flex items-center justify-center shrink-0">
                            <x-icon name="arrow-trending-up" class="w-5 h-5 text-high-text" />
                        </div>
                        <div>
                            <p class="text-xs text-muted-text font-medium">Promedio</p>
                            <p class="text-2xl font-bold {{ ($stats['average_score'] ?? 0) >= 60 ? 'text-high-text' : 'text-medium-text' }}">
                                {{ $stats['average_score'] ? round($stats['average_score']) . '%' : 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick actions bar --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-body-text">Historial de autodiagnósticos</h3>
                    <p class="text-sm text-muted-text">{{ $allAssessments->count() }} evaluación(es) registrada(s)</p>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Filter by company --}}
                    @if($companies->count() > 1)
                        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                            <select name="company_id" onchange="this.form.submit()"
                                    class="text-sm rounded-lg border-border-light shadow-sm focus:border-primary focus:ring-primary">
                                <option value="">Todas las empresas</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @endif
                    <a href="{{ route('diagnostic.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-hover transition">
                        <x-icon name="plus" class="w-4 h-4" />
                        Nuevo diagnóstico
                    </a>
                </div>
            </div>

            {{-- Assessments Table --}}
            <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg">
                @if($allAssessments->isEmpty())
                    <div class="p-12 text-center">
                        <x-icon name="clipboard-document" class="mx-auto h-16 w-16 text-muted-text" />
                        <h3 class="mt-4 text-lg font-medium text-body-text">No hay autodiagnósticos aún</h3>
                        <p class="mt-2 text-sm text-muted-text">Comience evaluando el cumplimiento de su empresa con la Ley 1581.</p>
                        <a href="{{ route('diagnostic.index') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-hover transition">
                            <x-icon name="plus" class="w-4 h-4" />
                            Comenzar autodiagnóstico
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-border-light">
                            <thead class="bg-bg-page">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-text uppercase">Empresa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-text uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-text uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-text uppercase">Puntaje</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-muted-text uppercase">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="bg-card-bg divide-y divide-border-light">
                                @foreach($allAssessments as $assessment)
                                    <tr class="hover:bg-bg-page/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <p class="text-sm font-medium text-body-text">{{ $assessment->company->name }}</p>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <p class="text-sm text-muted-text">{{ $assessment->created_at->format('d/m/Y') }}</p>
                                            <p class="text-xs text-muted-text/70">{{ $assessment->created_at->format('H:i') }}</p>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($assessment->status === 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-high-bg text-high-text">
                                                    Completado
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-medium-bg text-medium-text">
                                                    En progreso
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($assessment->status === 'completed' && $assessment->score !== null)
                                                @php
                                                    $scoreClass = $assessment->score >= 70 ? 'text-high-text' : ($assessment->score >= 40 ? 'text-medium-text' : 'text-low-text');
                                                    $scoreBg = $assessment->score >= 70 ? 'bg-high-bg' : ($assessment->score >= 40 ? 'bg-medium-bg' : 'bg-low-bg');
                                                @endphp
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold {{ $scoreClass }} {{ $scoreBg }}">
                                                    <span class="w-1.5 h-1.5 rounded-full currentColor"></span>
                                                    {{ $assessment->score }}%
                                                </span>
                                            @else
                                                <span class="text-sm text-muted-text">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            @if($assessment->status === 'completed')
                                                <a href="{{ route('diagnostic.results', $assessment) }}"
                                                   class="inline-flex items-center gap-1 text-sm font-medium text-primary hover:text-primary-hover transition">
                                                    Ver resultado
                                                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                                                </a>
                                            @else
                                                <a href="{{ route('diagnostic.show', $assessment) }}"
                                                   class="inline-flex items-center gap-1 text-sm font-medium text-primary hover:text-primary-hover transition">
                                                    Continuar
                                                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
