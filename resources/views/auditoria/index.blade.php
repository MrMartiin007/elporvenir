<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('🔍 Auditoría de Inventario - Historial') }}
        </h2>
    </x-slot>

    @if (session('success'))
        <script>Swal.fire('{{ session("success") }}', '', 'success');</script>
    @endif
    @if (session('error'))
        <script>Swal.fire('{{ session("error") }}', '', 'error');</script>
    @endif

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="mb-4">
            <a href="{{ route('auditoria.iniciar') }}" class="btn btn-warning fw-bold">
                ➕ Iniciar Auditoría Diaria
            </a>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-4">
            <div class="table-responsive-md mt-3">
                <table class="table table-hover table-bordered table-sm" id="tablaAuditorias">
                    <thead class="text-center table-dark">
                        <tr>
                            <th>#</th>
                            <th>Fecha de Auditoría</th>
                            <th>Iniciada por</th>
                            <th>Productos Auditados</th>
                            <th>Total Auditado (Costo)</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auditorias as $auditoria)
                            <tr class="align-middle text-center">
                                <td>{{ $auditoria->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($auditoria->fecha_auditoria)->format('d/m/Y H:i') }}</td>
                                <td>{{ $auditoria->user?->name ?? '—' }}</td>
                                <td><span class="badge bg-secondary">{{ $auditoria->cantidad_productos }}</span></td>
                                <td>Q {{ number_format($auditoria->total_auditado, 2) }}</td>
                                <td>
                                    @if($auditoria->estado == 1)
                                        <span class="badge bg-success">En progreso</span>
                                    @else
                                        <span class="badge bg-danger">Cerrada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($auditoria->estado == 1 && $auditoria->users_id == auth()->id())
                                        <a href="{{ route('auditoria.create') }}" class="btn btn-sm btn-warning">Continuar
                                            Auditoría</a>
                                    @else
                                        <a href="{{ route('auditoria.show', $auditoria->id) }}"
                                            class="btn btn-sm btn-info text-white">Ver Detalles</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No hay auditorías registradas aún.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if ($.fn.DataTable.isDataTable('#tablaAuditorias')) {
                $('#tablaAuditorias').DataTable().destroy();
            }
            $('#tablaAuditorias').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                },
                "order": [[0, "desc"]]
            });
        });
    </script>
</x-app-layout>