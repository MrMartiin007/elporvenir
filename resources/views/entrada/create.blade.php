<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Entrada') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg bg-div">
                <div class="p-6 text-gray-900">
                    @if (session('bad_status'))
                        <div class="mb-4">
                            <div class="alert alert-warning">
                                {{ session('bad_status') }}
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('entradas.store') }}" method="POST" enctype="multipart/form-data"
                        class="guardar" novalidate>
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="fecha_ingreso" :value="__('Fecha Ingreso')" />
                                <x-text-input id="fecha_ingreso" name="fecha_ingreso" type="date"
                                    class="mt-1 block w-full" value="{{ old('fecha_ingreso', date('Y-m-d')) }}"
                                    required />
                                <x-input-error :messages="$errors->get('fecha_ingreso')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="cantidad" :value="__('Cantidad')" />
                                <x-text-input id="cantidad" name="cantidad" type="number" min="1"
                                    class="mt-1 block w-full" value="{{ old('cantidad') }}" required />
                                <x-input-error :messages="$errors->get('cantidad')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="productos_id" :value="__('Producto')" />
                                <select id="productos_id" name="productos_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="" disabled selected>Seleccione un producto</option>
                                    @foreach ($productos as $producto)
                                        <option value="{{ $producto->id }}" {{ old('productos_id') == $producto->id ? 'selected' : '' }}>
                                            {{ $producto->detalle_producto }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('productos_id')" class="mt-2" />
                            </div>

                            
                        </div>
                         <div class="flex items-center mt-6 space-x-4">
                                <a href="{{ url()->previous() }}">
                                    <button type="button"
                                        class="bg-red-500 hover:bg-red-800 text-white font-bold py-2 px-4 border border-red-700 rounded">
                                        Regresar
                                    </button>
                                </a>

                                <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-800 text-white font-bold py-2 px-4 border border-blue-700 rounded">
                                    Guardar Entrada
                                </button>
                            </div>
                    </form>
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
                        title: 'Cargando...',
                        text: 'Por favor espera un momento',
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