<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Resumen de Auditoría #') . $auditoria->id }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="mb-4">
            <a href="{{ route('auditoria.index') }}" class="btn btn-secondary">
                ⬅️ Volver al listado
            </a>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-4 mb-4">
            <div class="row fw-bold text-center">
                <div class="col-md-3">
                    <span class="text-muted d-block text-uppercase text-xs">Fecha</span>
                    <span class="fs-5">{{ \Carbon\Carbon::parse($auditoria->fecha_auditoria)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="col-md-3">
                    <span class="text-muted d-block text-uppercase text-xs">Auditor</span>
                    <span class="fs-5">{{ $auditoria->user->name }}</span>
                </div>
                <div class="col-md-3">
                    <span class="text-muted d-block text-uppercase text-xs">Productos Auditados</span>
                    <span class="fs-5 text-primary">{{ $auditoria->cantidad_productos }}</span>
                </div>
                <div class="col-md-3">
                    <span class="text-muted d-block text-uppercase text-xs">Total Auditado</span>
                    <span class="fs-5 text-success">$ {{ number_format($auditoria->total_auditado, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-4">
            <h5 class="fw-bold mb-3">📋 Registros de auditoría</h5>
            <div class="table-responsive-md">
                <table class="table table-hover table-bordered table-sm" id="tablaDetalleAuditorias">
                    <thead class="text-center table-dark">
                        <tr>
                            <th>Hora</th>
                            <th>Producto</th>
                            <th>Stock Anterior</th>
                            <th>Stock Nuevo</th>
                            <th>Costo Ant.</th>
                            <th>Costo Nuevo</th>
                            <th>Venta Ant.</th>
                            <th>Venta Nueva</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auditoria->detalles as $detalle)
                            <tr class="align-middle text-center">
                                <td>{{ $detalle->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="text-start">
                                    <small class="text-muted">{{ $detalle->producto?->codigo_producto }}</small><br>
                                    <strong>{{ $detalle->producto?->detalle_producto ?? '—' }}</strong>
                                </td>
                                <td>{{ $detalle->stock_anterior }}</td>
                                <td>
                                    <span class="badge {{ $detalle->stock_anterior != $detalle->stock_nuevo ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $detalle->stock_nuevo }}
                                    </span>
                                </td>
                                <td>{{ number_format($detalle->precio_costo_anterior, 2) }}</td>
                                <td>{{ number_format($detalle->precio_costo_nuevo, 2) }}</td>
                                <td>{{ number_format($detalle->precio_venta_anterior, 2) }}</td>
                                <td>{{ number_format($detalle->precio_venta_nuevo, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No hay registros para mostrar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if ($.fn.DataTable.isDataTable('#tablaDetalleAuditorias')) {
                $('#tablaDetalleAuditorias').DataTable().destroy();
            }
            $('#tablaDetalleAuditorias').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                },
                "order": [[ 0, "desc" ]] // sort by hora desc
            });
        });
    </script>
</x-app-layout>
