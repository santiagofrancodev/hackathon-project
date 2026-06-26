<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-body-text leading-tight">
            {{ __('Registrar Empresa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-card-bg overflow-hidden shadow-sm border border-border-light sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('company.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-body-text">Nombre de la empresa</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="mt-1 block w-full rounded-md border-border-light shadow-sm focus:border-primary focus:ring-primary">
                            @error('name') <p class="mt-1 text-sm text-low-text">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="nit" class="block text-sm font-medium text-body-text">NIT</label>
                            <input type="text" name="nit" id="nit" value="{{ old('nit') }}" required
                                   class="mt-1 block w-full rounded-md border-border-light shadow-sm focus:border-primary focus:ring-primary">
                            @error('nit') <p class="mt-1 text-sm text-low-text">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="sector" class="block text-sm font-medium text-body-text">Sector</label>
                            <input type="text" name="sector" id="sector" value="{{ old('sector') }}"
                                   class="mt-1 block w-full rounded-md border-border-light shadow-sm focus:border-primary focus:ring-primary"
                                   placeholder="Ej: Tecnología, Salud, Financiero">
                            @error('sector') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-6">
                            <label for="size" class="block text-sm font-medium text-body-text">Tamaño de la empresa</label>
                            <select name="size" id="size"
                                    class="mt-1 block w-full rounded-md border-border-light shadow-sm focus:border-primary focus:ring-primary">
                                <option value="">Seleccioná un tamaño...</option>
                                <option value="small" {{ old('size') == 'small' ? 'selected' : '' }}>Pequeña</option>
                                <option value="medium" {{ old('size') == 'medium' ? 'selected' : '' }}>Mediana</option>
                                <option value="large" {{ old('size') == 'large' ? 'selected' : '' }}>Grande</option>
                            </select>
                            @error('size') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('diagnostic.index') }}" class="text-sm text-primary hover:text-primary-hover">&larr; Volver</a>
                            <button type="submit" class="px-6 py-2 bg-primary text-white font-semibold rounded-md hover:bg-primary-hover">
                                Guardar empresa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
