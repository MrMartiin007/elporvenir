<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Auditoría: Productos Eliminados de la Venta #') }}{{ $venta->id }}
        </h2>
    </x-slot>

    <div class="py-1 mt-4">
        <div class="container-fluid mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="row align-items-end">
                    <div class="col text-end mt-4 pe-5">
                        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Volver a Ventas
                        </a>
                    </div>
                </div>
                
                <div class="p-4 border-bottom bg-light mt-3">
                    <h4 class="mb-0 text-danger"><i class="fas fa-trash-alt me-2"></i> Historial de Productos Retirados
                        del Carrito</h4>
                    <p class="text-muted small mt-1 mb-0">Esta lista muestra los productos que fueron borrados de esta
                        venta específica antes de ser cobrada/cerrada.</p>
                </div>

                <div class="p-4 text-gray-900">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped" id="tablaEliminados">
                            <thead class="text-center" style="background-color: #f8d7da; color: #721c24;">
                                <tr>
                                    <th>Fecha de Eliminación</th>
                                    <th>Cajero Responsable</th>
                                    <th>Código</th>
                                    <th>Foto</th>
                                    <th>Producto</th>
                                    <th>Cantidad Borrada</th>
                                    <th>Precio Unitario</th>
                                    <th>Importe Restado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($eliminados as $registro)
                                    <tr class="align-middle text-center">
                                        <td>{{ $registro->created_at->format('d/m/Y h:i A') }}</td>
                                        <td class="fw-bold">{{ $registro->user->name ?? 'Usuario Desconocido' }}</td>
                                        <td>{{ $registro->producto->codigo_producto ?? 'N/A' }}</td>
                                        <td>
                                            @if($registro->producto && $registro->producto->foto_producto)
                                                <img src="{{ asset('storage/' . $registro->producto->foto_producto) }}" 
                                                    alt="Foto" 
                                                    class="rounded-circle object-cover border shadow-sm"
                                                    style="width: 45px; height: 45px;">
                                            @else
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto border" style="width: 45px; height: 45px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-start">
                                            {{ $registro->producto->detalle_producto ?? 'Producto borrado de la BD' }}</td>
                                        <td><span class="badge bg-danger fs-6">{{ $registro->cantidad }}</span></td>
                                        <td>Q {{ number_format($registro->precio_unitario, 2) }}</td>
                                        <td class="fw-bold text-danger">- Q {{ number_format($registro->importe_total, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center p-5 text-muted">
                                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                            <h5>Todo limpio</h5>
                                            <p>Ningún producto fue eliminado durante esta venta.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    $(document).ready(function () {
        $('#tablaEliminados').DataTable({
            responsive: true,
            language: {
                "emptyTable": "Ningún producto fue eliminado durante esta venta",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "lengthMenu": "Mostrar _MENU_ registros",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron resultados",
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