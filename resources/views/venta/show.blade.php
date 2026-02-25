<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalle de Venta') }} {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d-m-Y') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="row">
                <!-- 📋 Detalle Principal -->
                <div class="col-md-9">
                    <div class="bg-white shadow-sm sm:rounded-lg mb-4">
                        <div class="row align-items-end">
                            <div class="col text-end mt-4 pe-5">
                                <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Volver a Ventas
                                </a>
                            </div>
                        </div>
                        <div class="p-6 text-gray-900">
                            <div class="table-responsive-md">
                                <table class="table table-hover table-bordered" id="tablaProductos">
                                    <thead class="text-center" style="background-color: #ffb6c1; color: #000;">
                                        <tr>
                                            <th>No</th>
                                            <th>Código Producto</th>
                                            <th>Detalle Producto</th>
                                            <th>Foto</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unitario</th>
                                            <th>Descuento</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($venta->detalles as $detalle)
                                            <tr class="text-center align-middle">
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="align-middle text-center">
                                                    {{ $detalle->producto->codigo_producto }}</td>
                                                <td>{{ $detalle->producto->detalle_producto }}</td>
                                                <td>
                                                    @if ($detalle->producto->foto_producto)
                                                        <img src="{{ asset('storage/' . $detalle->producto->foto_producto) }}"
                                                            class="rounded-circle object-cover"
                                                            style="width: 45px; height: 45px;" alt="Foto producto">
                                                    @else
                                                        <span class="text-muted">Sin imagen</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle text-center">{{ $detalle->cantidad }}</td>
                                                <td>Q {{ number_format($detalle->precio_unitario, 2) }}</td>
                                                <td>Q {{ number_format($detalle->descuento ?? 0, 2) }}</td>
                                                <td>
                                                    Q
                                                    {{ number_format(($detalle->precio_unitario - ($detalle->descuento ?? 0)) * $detalle->cantidad, 2) }}
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
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 📋 Resumen lateral -->
                <div class="col-md-3">
                    <div class="card mb-3">
                        <div class="card-header text-dark">
                            <strong>Resumen de la Venta</strong>
                        </div>
                        <div class="card-body">
                            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d-m-Y') }}</p>
                            <p><strong>Estado:</strong> 
                                @if($venta->estado == 1)
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-secondary">Cerrada</span>
                                @endif
                            </p>
                            <p><strong>Productos:</strong> {{ $venta->cantidad_productos }}</p>
                            <p><strong>Total:</strong>
                                <span class="text-danger fw-bold">
                                    Q{{ number_format($venta->total_vendido, 2) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Card de Códigos No Encontrados -->
                    <div class="card mb-3">
                        <div class="card-header text-dark">
                            <strong>⚠️ Códigos No Encontrados</strong>
                            @if(isset($venta->codigosNoEncontrados) && $venta->codigosNoEncontrados->count() > 0)
                                <span class="badge bg-danger">{{ $venta->codigosNoEncontrados->count() }}</span>
                            @endif
                        </div>
                        <div class="card-body">
                            @if(isset($venta->codigosNoEncontrados) && $venta->codigosNoEncontrados->count() > 0)
                                <div class="list-group">
                                    @foreach($venta->codigosNoEncontrados as $codigo)
                                        <div class="list-group-item p-3 border-start border-4 border-warning">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1 me-3">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="badge bg-dark me-2">
                                                            <i class="fas fa-barcode"></i>
                                                        </span>
                                                        <strong class="text-dark d-inline-block text-truncate" style="max-width: 100px;" title="{{ $codigo->codigo }}">{{ $codigo->codigo }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted text-center mb-0">
                                    <small>No hay códigos sin registrar</small>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

            </div> <!-- End row -->

            {{-- Scripts para DataTables --}}
            <script>
                $(document).ready(function () {
                    let table = $('#tablaProductos').DataTable({
                        responsive: true,
                        scrollX: true,
                        autoWidth: false,
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

                    table.columns.adjust();

                    $(window).resize(function () {
                        table.columns.adjust();
                    });
                });
            </script>
</x-app-layout>