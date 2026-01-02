<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ver Marca') }}
        </h2>
    </x-slot>

    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg bg-div">
                <div class="p-6 text-gray-900">
                    
                    <!-- Header con botón de regreso -->
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Información de la Marca</h3>
                        <a href="{{ route('marcas.index') }}">
                            <button type="button"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 border border-gray-700 rounded">
                                Regresar
                            </button>
                        </a>
                    </div>

                    <div class="max-w-4xl mx-auto">
                        <!-- Logo de la Marca -->
                        @if($marca->foto_marca)
                            <div class="mb-6 text-center">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Logo de la Marca</label>
                                <div class="inline-block p-4 bg-gray-50 rounded-lg border-2 border-gray-200">
                                    <img src="{{ asset('storage/' . $marca->foto_marca) }}" 
                                         alt="Logo {{ $marca->nombre_marca }}"
                                         class="max-h-48 w-auto mx-auto rounded-lg shadow-sm">
                                </div>
                            </div>
                        @endif

                        <!-- Nombre de la Marca -->
                        <div class="grid grid-cols-1 gap-6">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Marca</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $marca->nombre_marca }}</p>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex items-center justify-end gap-4 mt-6">
                            <a href="{{ route('marcas.edit', $marca->id) }}">
                                <button type="button"
                                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 border border-yellow-700 rounded">
                                    Editar
                                </button>
                            </a>
                            <form action="{{ route('marcas.destroy', $marca->id) }}" method="POST" class="inline" 
                                  onsubmit="return confirm('¿Estás seguro de eliminar esta marca?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-500 hover:bg-red-800 text-white font-bold py-2 px-4 border border-red-700 rounded">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
