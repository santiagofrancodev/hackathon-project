<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-body-text leading-tight">
            {{ __('Autodiagnóstico de Cumplimiento - Ley 1581') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-high-bg border border-high-text/30 text-high-text rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if($companies->isEmpty())
                <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-16 w-16 text-muted-text" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-body-text">No hay empresas registradas</h3>
                        <p class="mt-2 text-sm text-muted-text">Registre su empresa primero para comenzar el autodiagnóstico.</p>
                        <a href="{{ route('company.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-hover">
                            + Registrar empresa
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($categories as $category)
                        <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-body-text">{{ $category->name }}</h3>
                                    <span class="text-sm font-medium text-primary">Máx. {{ $category->max_percentage }}%</span>
                                </div>
                                <p class="mt-2 text-sm text-muted-text">{{ $category->description }}</p>
                                <div class="mt-4">
                                    <div class="text-sm text-muted-text">
                                        {{ $category->questions->count() }} preguntas
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-body-text mb-4">Iniciar autodiagnóstico</h3>
                        <form action="{{ route('diagnostic.store') }}" method="POST" class="flex items-end gap-4">
                            @csrf
                            <div class="flex-1">
                                <label for="company_id" class="block text-sm font-medium text-body-text">Seleccione la empresa a evaluar</label>
                                <select name="company_id" id="company_id" required class="mt-1 block w-full rounded-md border-border-light shadow-sm focus:border-primary focus:ring-primary">
                                    <option value="">Seleccione una empresa...</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }} ({{ $company->nit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="px-6 py-2 bg-primary text-white font-semibold rounded-md hover:bg-primary-hover">
                                Comenzar
                            </button>
                        </form>
                        <div class="mt-4">
                            <a href="{{ route('company.create') }}" class="text-sm text-primary hover:text-primary-hover">+ Registrar otra empresa</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
