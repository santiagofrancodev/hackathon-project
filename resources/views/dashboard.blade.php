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

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg p-6">
                    <p class="text-sm text-muted-text">Empresas</p>
                    <p class="text-3xl font-bold text-body-text">{{ $stats['companies_count'] }}</p>
                </div>
                <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg p-6">
                    <p class="text-sm text-muted-text">Autodiagnósticos</p>
                    <p class="text-3xl font-bold text-body-text">{{ $stats['total_assessments'] }}</p>
                </div>
                <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg p-6">
                    <p class="text-sm text-muted-text">Completados</p>
                    <p class="text-3xl font-bold text-body-text">{{ $stats['completed_assessments'] }}</p>
                </div>
                <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg p-6">
                    <p class="text-sm text-muted-text">Puntaje promedio</p>
                    <p class="text-3xl font-bold {{ ($stats['average_score'] ?? 0) >= 60 ? 'text-high-text' : 'text-medium-text' }}">
                        {{ $stats['average_score'] ? round($stats['average_score']) . '%' : 'N/A' }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Assessments -->
                <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-body-text mb-4">Autodiagnósticos recientes</h3>
                        @if($recentAssessments->isEmpty())
                            <p class="text-sm text-gray-500">No hay autodiagnósticos aún.</p>
                            <a href="{{ route('diagnostic.index') }}" class="mt-2 inline-block text-sm text-primary hover:text-primary-hover">Comenzar uno nuevo &rarr;</a>
                        @else
                            <div class="space-y-3">
                                @foreach($recentAssessments as $assessment)
                                    <div class="flex items-center justify-between p-3 bg-bg-page rounded-lg border border-border-light">
                                        <div>
                                            <p class="text-sm font-medium text-body-text">{{ $assessment->company->name }}</p>
                                            <p class="text-xs text-muted-text">{{ $assessment->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="text-right">
                                            @if($assessment->status === 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-high-bg text-high-text">
                                                    {{ $assessment->score }}%
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-medium-bg text-medium-text">
                                                    En progreso
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <a href="{{ route('diagnostic.index') }}" class="mt-4 inline-block text-sm text-primary hover:text-primary-hover">Ver todos &rarr;</a>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-body-text mb-4">Acciones rápidas</h3>
                        <div class="space-y-3">
                            <a href="{{ route('diagnostic.index') }}" class="block p-3 bg-primary/5 rounded-lg hover:bg-primary/10 transition border border-primary/10">
                                <p class="text-sm font-medium text-primary">Nuevo autodiagnóstico</p>
                                <p class="text-xs text-primary/70">Evalúa el cumplimiento de tu empresa con la Ley 1581</p>
                            </a>
                            <a href="{{ route('company.create') }}" class="block p-3 bg-high-bg rounded-lg hover:bg-high-text/10 transition border border-high-text/10">
                                <p class="text-sm font-medium text-high-text">Registrar empresa</p>
                                <p class="text-xs text-high-text/70">Agrega una nueva empresa para evaluar</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
