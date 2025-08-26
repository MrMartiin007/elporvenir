<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registro de Venta ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">

            <div class="row">
                <!-- 🧾 Sección Principal -->
                <div class="col-md-8">
                    <div class="bg-white shadow-sm rounded p-4">
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success m-4">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        @if ($message = Session::get('bad_status'))
                            <div class="alert alert-warning mb-4">
                                <p>{{ $message }}</p>
                            </div>
                        @endif

                        <div class="d-flex align-items-center gap-2 mb-3">
                            <form method="GET" action="{{ route('ventas.create') }}" id="formScan" class="flex-grow-1">
                                <input type="text" name="scan" id="scan" autofocus autocomplete="off"
                                    class="border border-gray-300 rounded form-control form-control-sm w-50"
                                    placeholder="Escanea un código...">
                            </form>
                            <form method="POST" action="{{ route('ventas.cerrar', $venta->id) }}"
                                onsubmit="return confirm('¿Estás seguro de cerrar esta venta?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-lock"></i> Cerrar Venta
                                </button>
                            </form>
                        </div>

                        <strong class="text-center align-middle"> Productos Vendidos</strong>
                        @if($venta->detalles->count() > 0)
                            <table class="table table-bordered table-striped " id="tablaProductos">
                                <thead class="text-center">
                                    <tr>
                                        <th>#</th>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th>Foto</th>
                                        <th>Cantidad</th>
                                        <th>Precio U</th>
                                        <th>Descuento</th>
                                        <th>Subtotal</th>
                                        <th>Acccion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($venta->detalles()->orderBy('created_at', 'desc')->get() as $detalle)
                                        <tr class="text-center align-middle">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $detalle->producto->codigo_producto }}</td>
                                            <td>{{ $detalle->producto->detalle_producto }}</td>
                                            <td>
                                                @if($detalle->producto->foto_producto)
                                                    <img src="{{ asset('storage/' . $detalle->producto->foto_producto) }}"
                                                        class="rounded-circle object-cover cursor-pointer"
                                                        style="width: 45px; height: 45px;" alt="Foto producto"
                                                        data-bs-toggle="modal" data-bs-target="#fotoModal"
                                                        onclick="mostrarImagen('{{ asset('storage/' . $detalle->producto->foto_producto) }}')">
                                                @else
                                                    <span class="text-muted">Sin imagen</span>
                                                @endif

                                            </td>
                                            <td class="text-center align-middle">
                                                <input type="number" name="cantidad"
                                                    value="{{ old('cantidad', $detalle->cantidad) }}" min="1"
                                                    form="f-actualizar-{{ $detalle->id }}"
                                                    style="
                                                            border: none; 
                                                            border-bottom: 1px solid #333; 
                                                            outline: none; 
                                                            width: 80px; 
                                                            text-align: center;
                                                            padding: 2px 4px;
                                                            background: transparent;
                                                        " class="form-control-sm">
                                            </td>
                                            <td>Q {{ number_format($detalle->precio_unitario, 2) ?? 'favor verifique el precio'}}</td>
                                            <td class="text-center align-middle">
                                                <input type="number" name="descuento"
                                                    value="{{ old('descuento', $detalle->descuento) }}" step="0.01" min="0"
                                                    max="{{ $detalle->precio_unitario }}" form="f-actualizar-{{ $detalle->id }}"
                                                    style="
                                                            border: none; 
                                                            border-bottom: 1px solid #333; 
                                                            outline: none; 
                                                            width: 80px; 
                                                            text-align: center;
                                                            padding: 2px 4px;
                                                            background: transparent;
                                                        " class="form-control-sm">
                                            </td>
                                            <td>Q
                                                {{ number_format($detalle->cantidad * ($detalle->precio_unitario - ($detalle->descuento ?? 0)), 2) }}
                                            </td>
                                            <td class="text-center align-middle">
                                                <form id="f-actualizar-{{ $detalle->id }}"
                                                    action="{{ route('ventas.update', $detalle->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>

                                                {{-- Botón eliminar (opcional, si ya lo tienes en esta misma celda) --}}
                                                <form action="{{ route('ventas.destroy', $detalle->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('¿Eliminar este producto de la venta?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="text-end">
                                        <th colspan="7">Total Vendido</th>
                                        <th>Q {{ number_format($venta->total_vendido, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        @endif

                        <div class="mt-3">
                            <a href="{{ route('ventas.index') }}" class="btn btn-secondary">Volver</a>
                        </div>
                    </div>
                </div>
                <!-- 📋 Resumen lateral -->
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header  text-dark">
                            <strong>Resumen de la Venta</strong>
                        </div>
                        <div class="card-body">
                            <p><strong>Fecha:</strong> {{ $venta->fecha_venta }}</p>
                            <p><strong>Productos:</strong> {{ $venta->cantidad_productos }}</p>
                            <p><strong>Total:</strong>
                                <span class="text-danger fw-bold">
                                    Q{{ number_format($venta->total_vendido, 2) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div> <!-- End Row -->
        </div>
    </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#tablaProductos').DataTable({
                ordering: false,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                language: {
                    "emptyTable": "No hay productos registrados",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ productos",
                    "infoEmpty": "Mostrando 0 a 0 de 0 productos",
                    "infoFiltered": "(filtrado de _MAX_ productos)",
                    "lengthMenu": "Mostrar _MENU_ productos",
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
        });
    </script>
    <!-- Modal para mostrar imagen -->
    <div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Foto del producto" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>



    <script>
        function mostrarImagen(ruta) {
            document.getElementById('modalImage').src = ruta;
        }
    </script>
</x-app-layout>