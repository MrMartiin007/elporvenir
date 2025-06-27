<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalle de Venta') }} {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d-m-Y') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
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
                                        <td class="align-middle text-center">{{ $detalle->producto->codigo_producto }}</td>
                                        <td>{{ $detalle->producto->detalle_producto }}</td>
                                        <td>
                                            @if ($detalle->producto->foto_producto)
                                                <img src="{{ asset('storage/' . $detalle->producto->foto_producto) }}"
                                                    class="rounded-circle object-cover" style="width: 45px; height: 45px;"
                                                    alt="Foto producto">
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
    </div>

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