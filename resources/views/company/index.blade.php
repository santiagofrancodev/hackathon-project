<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-body-text leading-tight">
            {{ __('Mis Empresas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-high-bg border border-high-text/30 text-high-text rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-end mb-4">
                <a href="{{ route('company.create') }}" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-hover text-sm font-medium">
                    + Nueva empresa
                </a>
            </div>

            @if($companies->isEmpty())
                <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg p-6 text-center">
                    <p class="text-muted-text">No hay empresas registradas.</p>
                    <a href="{{ route('company.create') }}" class="mt-2 inline-block text-sm text-primary hover:text-primary-hover">Registra tu primera empresa &rarr;</a>
                </div>
            @else
                <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg">
                    <table class="min-w-full divide-y divide-border-light">
                        <thead class="bg-bg-page">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-text uppercase">Empresa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-text uppercase">NIT</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-text uppercase">Sector</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-text uppercase">Tamaño</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-text uppercase">Evaluaciones</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-text uppercase">Creada</th>
                            </tr>
                        </thead>
                        <tbody class="bg-card-bg divide-y divide-border-light">
                            @foreach($companies as $company)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-body-text">{{ $company->name }}</td>
                                    <td class="px-6 py-4 text-sm text-muted-text">{{ $company->nit }}</td>
                                    <td class="px-6 py-4 text-sm text-muted-text">{{ $company->sector ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm text-muted-text">
                                        @switch($company->size)
                                            @case('small') Pequeña @break
                                            @case('medium') Mediana @break
                                            @case('large') Grande @break
                                            @default —
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 text-sm text-muted-text">{{ $company->assessments_count }}</td>
                                    <td class="px-6 py-4 text-sm text-muted-text">{{ $company->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
