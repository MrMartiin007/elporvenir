<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lista de Entradas al Inventario') }}
        </h2>
    </x-slot>

    @if(session('success_title') && session('success_text'))
        <script>
            Swal.fire({
                icon: 'success',
                title: "{{ session('success_title') }}",
                html: "<small class='text-white bg-green-600 px-2 py-1 rounded'>{{ session('success_text') }}</small>",
                confirmButtonText: "OK"
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            Swal.fire({
                title: '{{ session("success") }}',
                icon: 'success',
                didClose: () => {
                    const input = document.getElementById('codigoBuscar');
                    if (input) {
                        input.value = '';
                        input.focus();
                    }
                }
            });
        </script>
    @endif

    @if (session('error'))
        <script>Swal.fire('{{ session("error") }}', '', 'error');</script>
    @endif

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 bg-white shadow-sm sm:rounded-lg p-4">
            <form method="GET" action="{{ route('entradas.index') }}" class="flex gap-3">
                <input type="text" name="buscar" id="codigoBuscar" class="border border-gray-300 rounded form-control"
                    placeholder="Escanee o escriba el código del producto" autofocus />
            </form>
        </div>

        @if(request('buscar') && $productosFiltrados->count() === 1 && $productosFiltrados->first()->codigo_producto === request('buscar'))
            @php $producto = $productosFiltrados->first(); @endphp
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const modal = new bootstrap.Modal(document.getElementById('modalEntrada'));
                    modal.show();

                    setTimeout(() => {
                        document.getElementById('cantidad')?.focus();
                    }, 800);
                });
            </script>


            <div class="modal fade show d-block" id="modalEntrada" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 12px;">
                        <div class="modal-header">
                            <h5 class="modal-title">Registrar Entrada</h5>
                            <a href="{{ route('entradas.index') }}" class="btn-close"></a>
                        </div>
                        <div class="modal-body">
                            
                            <div class="text-center mb-3">
                                <x-input-label for="foto_prodcuto" :value="__('Foto del producto')" />
                                <img src="{{ asset('storage/' . $producto->foto_producto) }}" alt="Foto" class="mx-auto"
                                    style="height: 100px; border-radius: 8px;">
                            </div>

                            <div class="row mb-3">
                                <div class="col">
                                    <x-input-label for="codigo_producto" :value="__('Código del Producto')" />
                                    <x-text-input id="codigo_producto" type="text" class="mt-1 block w-full"
                                        value="{{ $producto->codigo_producto }}" disabled />
                                </div>
                                <div class="col">
                                    <x-input-label for="detalle_producto" :value="__('Nombre del Producto')" />
                                    <x-text-input id="detalle_producto" type="text" class="mt-1 block w-full"
                                        value="{{ $producto->detalle_producto }}" disabled />
                                </div>
                            </div>
                             <div class="row mb-3">
                                <div class="col">
                                    <x-input-label for="stock" :value="__('Stock')" />
                                    <x-text-input id="stock" type="text" class="mt-1 block w-full"
                                        value="{{ $producto->stock }} Unidades" disabled />
                                </div>
                                <div class="col">
                                    <x-input-label for="precio_costo" :value="__('Precio Costo')" />
                                    <x-text-input id="precio_costo" type="text" class="mt-1 block w-full"
                                        value="{{ $producto->precio_costo }} " disabled />
                                </div>
                            </div>

                            <form id="formEntrada" action="{{ route('entradas.store') }}" method="POST" class="guardar">
                                @csrf
                                <input type="hidden" name="productos_id" value="{{ $producto->id }}">

                                <div class="row mb-3">
                                    <div class="col">
                                        <x-input-label for="fecha_ingreso" :value="__('Fecha Ingreso')" />
                                        <x-text-input id="fecha_ingreso" name="fecha_ingreso" type="date"
                                            class="mt-1 block w-full" value="{{ old('fecha_ingreso', date('Y-m-d')) }}"
                                            required />
                                    </div>

                                    <div class="col">
                                        <x-input-label for="cantidad" :value="__('Cantidad')" />
                                        <x-text-input id="cantidad" name="cantidad" type="number" min="1"
                                            class="mt-1 block w-full" required />
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <a href="{{ route('entradas.index') }}" class="btn btn-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">Guardar Entrada</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow-sm sm:rounded-lg p-4">
            <div class="table-responsive-md">
                <table class="table table-hover table-bordered" id="tablaEntradas">
                    <thead class="text-center">
                        <tr>
                            <th>#</th>
                            <th>Fecha Ingreso</th>
                            <th>Cantidad</th>
                            <th>Producto</th>
                            <th>Foto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entradas as $entrada)
                            <tr class="align-middle text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $entrada->fecha_ingreso }}</td>
                                <td>{{ $entrada->cantidad }}</td>
                                <td>{{ $entrada->producto->detalle_producto }}</td>
                                <td><img src="{{ asset('storage/' . $entrada->producto->foto_producto) }}" alt="Foto"
                                        style="height: 50px; border-radius: 8px;"></td>
                                <td>
                                    <a href="{{ route('entradas.show', $entrada->id) }}" class="btn btn-sm btn-info"><i
                                            class="fa fa-eye"></i></a>
                                    <a href="{{ route('entradas.edit', $entrada->id) }}" class="btn btn-sm btn-success"><i
                                            class="fa fa-edit"></i></a>
                                    <form action="{{ route('entradas.destroy', $entrada->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('¿Estás seguro de eliminar esta entrada?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            let table = $('#tablaEntradas').DataTable({
                responsive: true,
                scrollX: true,
                autoWidth: false,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                language: {
                    "emptyTable": "No hay entradas registradas",
                    "lengthMenu": "Mostrar _MENU_ entradas",
                    "search": "Buscar:",
                    "zeroRecords": "No se encontraron coincidencias",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                }
            });
            table.columns.adjust();
            $(window).resize(() => table.columns.adjust());
        });

        document.querySelectorAll('.guardar').forEach(form => {
            form.addEventListener('submit', function (e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    form.reportValidity();
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