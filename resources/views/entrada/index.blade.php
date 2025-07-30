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
                                    <x-input-label for="Marca" :value="__('Marca')" />
                                    <x-text-input id="marca" type="text" class="mt-1 block w-full"
                                        value="{{ $producto->marca->nombre_marca }}" disabled />
                                </div>
                            </div>



                            <form id="formEntrada" action="{{ route('entradas.store') }}" method="POST" class="guardar">
                                @csrf
                                <input type="hidden" name="productos_id" value="{{ $producto->id }}">
                                @php
                                    $costo = $ultimaEntrada->precio_costo ?? '';
                                    $venta = $ultimaEntrada->precio_venta ?? '';
                                    $docena = $ultimaEntrada->precio_docena ?? '';
                                @endphp

                                <div class="row mb-3">
                                    <div class="col">
                                        <x-input-label for="precio_costo" :value="__('Nuevo Precio Costo?')" />
                                        <x-text-input id="precio_costo" name="precio_costo" type="number" step="0.01"
                                            class="mt-1 block w-full" value="{{ $costo }}" />
                                    </div>
                                    <div class="col">
                                        <x-input-label for="precio_venta" :value="__('Precio Venta')" />
                                        <x-text-input id="precio_venta" name="precio_venta" type="number" step="0.01"
                                            class="mt-1 block w-full" value="{{ $venta }}" />
                                    </div>
                                    <div class="col">
                                        <x-input-label for="precio_docena" :value="__('Precio Docena')" />
                                        <x-text-input id="precio_docena" name="precio_docena" type="number" step="0.01"
                                            class="mt-1 block w-full" value="{{ $docena }}" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col">
                                        <x-input-label for="fecha_ingreso" :value="__('Fecha y Hora de Ingreso')" />
                                        <x-text-input id="fecha_ingreso" name="fecha_ingreso" type="text"
                                            class="mt-1 block w-full"
                                            value="{{ old('fecha_ingreso', \Carbon\Carbon::now()->format('Y-m-d H:i:s')) }}"
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
            <strong> Ultimas Entradas</strong>
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
                                <td class="align-middle text-center">{{ $entrada->fecha_ingreso }}</td>
                                <td class="align-middle text-center">{{ $entrada->cantidad }}</td>
                                <td>{{ $entrada->producto->detalle_producto ?? 'sin detalle'}}</td>
                                <td>
                                    @if ($entrada->producto->foto_producto)
                                        <img src="{{ asset('storage/' . $entrada->producto->foto_producto) }}" alt="Foto"
                                            style="height: 50px; border-radius: 8px;">
                                    @else
                                        <img src="{{ asset('images/sin-foto.png') }}" alt="Sin foto"
                                            style="height: 50px; border-radius: 8px;">
                                    @endif
                                </td>

                                <form action="{{ route('entradas.destroy', $entrada->id) }}" method="POST" class="d-inline">
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
    <script>
        const formatCurrencyInputs = ['precio_costo', 'precio_venta', 'precio_docena'];

        formatCurrencyInputs.forEach(id => {
            const input = document.getElementById(id);
            if (!input) return;

            // Al iniciar, si está vacío, colocar "0.00"
            if (input.value.trim() === '') input.value = '0.00';

            input.addEventListener('input', () => {
                let raw = input.value.replace(/[^0-9]/g, ''); // solo números, eliminar todo menos dígitos

                if (raw === '') {
                    // Si borran todo, ponemos "0."
                    input.value = '0.';
                    return;
                }

                // Convertimos a número y lo dividimos entre 100 para poner decimal
                let num = parseInt(raw, 10);
                if (isNaN(num)) num = 0;

                // Formateamos con dos decimales
                let formatted = (num / 100).toFixed(2);

                input.value = formatted;
            });

            input.addEventListener('blur', () => {
                // Si al salir está vacío o inválido, poner 0.00
                if (input.value.trim() === '' || isNaN(parseFloat(input.value))) {
                    input.value = '0.00';
                }
            });
        });

        const inputCosto = document.getElementById('precio_costo');
        const inputVenta = document.getElementById('precio_venta');
        const inputDocena = document.getElementById('precio_docena');

        function actualizarPrecios() {
            let costo = parseFloat(inputCosto.value);
            if (isNaN(costo)) costo = 0;

            inputVenta.value = (costo * 1.25).toFixed(2);
            inputDocena.value = (costo * 1.15).toFixed(2);
        }

        if (inputCosto) {
            inputCosto.addEventListener('input', actualizarPrecios);
            inputCosto.addEventListener('blur', () => {
                if (inputCosto.value.trim() === '' || isNaN(parseFloat(inputCosto.value))) {
                    inputCosto.value = '0.00';
                }
                actualizarPrecios();
            });
            if (inputCosto.value.trim() === '') inputCosto.value = '0.00';
        }

    </script>

</x-app-layout>