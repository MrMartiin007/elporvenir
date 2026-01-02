<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lista de Facturas') }}
        </h2>
    </x-slot>

    @if(session('success'))
        <script>
            Swal.fire({
                title: '{{ session("success") }}',
                icon: 'success',
                confirmButtonText: "OK"
            });
        </script>
    @endif

    <div class="py-1">
        <div class="container-fluid">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                    <a href="{{ route('facturas.create') }}" class="btn text-white" style="background-color: #d63384;">
                        <i class="fas fa-plus me-2"></i> Nueva Factura
                    </a>

                    <form method="GET" action="{{ route('facturas.index') }}" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="buscar" value="{{ request('buscar') }}"
                                class="form-control border-pink" placeholder="Buscar...">
                            <button type="submit" class="btn text-white" style="background-color: #d63384;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tablaFacturas">
                            <thead class="text-center" style="background-color: #fce4ec; color: #880e4f;">
                                <tr>
                                    <th>#</th>
                                    <th>N° Factura</th>
                                    <th>Empresa</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Foto</th>
                                    <th>Fecha Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($facturas as $factura)
                                    <tr class="text-center align-middle">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $factura->numero_factura }}</td>
                                        <td>{{ $factura->empresa->nombre_empresa ?? 'Sin empresa' }}</td>
                                        <td>Q. {{ number_format($factura->monto, 2) }}</td>
                                        <td>
                                            <span class="badge 
                                                                                                                {{ $factura->estado == 2 ? 'bg-success' : '' }}
                                                                                                                {{ $factura->estado == 1 ? 'bg-warning' : '' }}
                                                                                                                {{ $factura->estado == 0 ? 'bg-danger' : '' }}
                                                                                                                {{ $factura->estado == 3 ? 'bg-info' : '' }}
                                                                                                                {{ $factura->estado == 4 ? 'bg-secondary' : '' }}
                                                                                                                {{ $factura->estado == 5 ? 'bg-primary' : '' }}
                                                                                                            ">
                                                @if($factura->estado == 0) Anulada @endif
                                                @if($factura->estado == 1) Pendiente @endif
                                                @if($factura->estado == 2) Pagada (Efectivo) @endif
                                                @if($factura->estado == 3) Pagada (Cheque) @endif
                                                @if($factura->estado == 4) Pagada (Tarjeta) @endif
                                                @if($factura->estado == 5) Pagada (Depósito) @endif
                                            </span>
                                        </td>
                                        <td>
                                            @if($factura->foto_fac)
                                                <img src="{{ asset('storage/' . $factura->foto_fac) }}"
                                                    class="rounded-circle object-cover cursor-pointer"
                                                    style="width: 45px; height: 45px;" alt="Foto Factura" data-bs-toggle="modal"
                                                    data-bs-target="#fotoModal"
                                                    onclick="mostrarImagen('{{ asset('storage/' . $factura->foto_fac) }}')">
                                            @else
                                                <span class="text-muted">Sin foto</span>
                                            @endif
                                        </td>
                                        <td>{{ $factura->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @if($factura->estado == 1)
                                                <a href="{{ route('facturas.liquidar', $factura->id) }}"
                                                    class="btn btn-sm btn-success me-1" title="Liquidar">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <form action="{{ route('facturas.destroy', $factura->id) }}" method="POST"
                                                    class="d-inline form-anular">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Anular">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                                <a href="{{ route('facturas.show', $factura->id) }}"
                                                    class="btn btn-sm btn-primary me-1" title="Ver Detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('facturas.edit', $factura->id) }}"
                                                    class="btn btn-sm btn-warning me-1" title="Editar">
                                                    <i class="fas fa-pen"></i>
                                                </a>

                                            @else
                                                <a href="{{ route('facturas.show', $factura->id) }}"
                                                    class="btn btn-sm btn-primary me-1" title="Ver Detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Links -->
                    <div class="mt-4">
                        {{ $facturas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Modal para mostrar imagen -->
    <div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Foto de Factura" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>

    <script>
        function mostrarImagen(ruta) {
            document.getElementById('modalImage').src = ruta;
        }

        $(document).ready(function () {
            $('.form-anular').submit(function (e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Anular Factura?',
                    text: "La factura pasará a estado ANULADA. Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, anular',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>