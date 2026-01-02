<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Empresa') }}
        </h2>
    </x-slot>

    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('empresas.update', $empresa->id) }}" method="POST" class="guardar">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="nombre_empresa" :value="__('Nombre de la Empresa')" />
                            <x-text-input id="nombre_empresa" name="nombre_empresa" type="text"
                                class="mt-1 block w-full" :value="old('nombre_empresa', $empresa->nombre_empresa)"
                                required autofocus />
                            <x-input-error :messages="$errors->get('nombre_empresa')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="proveedor" :value="__('Nombre del Proveedor')" />
                            <x-text-input id="proveedor" name="proveedor" type="text" class="mt-1 block w-full"
                                :value="old('proveedor', $empresa->proveedor)" required />
                            <x-input-error :messages="$errors->get('proveedor')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <a href="{{ route('empresas.index') }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Actualizar Empresa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.guardar').forEach(form => {
            form.addEventListener('submit', function (e) {
                if (!form.checkValidity()) {
                    form.reportValidity();
                    e.preventDefault();
                    return;
                }
                e.preventDefault();
                Swal.fire({
                    title: 'Actualizando...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                        form.submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>