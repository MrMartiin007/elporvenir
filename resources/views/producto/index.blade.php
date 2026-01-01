<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lista de productos registrados') }}
        </h2>
    </x-slot>

    {{-- Notificaciones SweetAlert --}}
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
        <script>Swal.fire('{{ session("success") }}', '', 'success');</script>
    @endif

    @if (session('error'))
        <script>Swal.fire('{{ session("error") }}', '', 'error');</script>
    @endif

    <div class="py-9">
        <div class="container-fluid">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                    <a href="{{ route('productos.create') }}" class="btn text-white" style="background-color: #d63384;">
                        <i class="fa fa-plus me-2"></i> Crear Nuevo
                    </a>

                    <form method="GET" action="{{ route('productos.index') }}" class="d-flex">
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
                        <table class="table table-hover table-bordered align-middle" id="tablaProductos">
                            <thead class="text-center" style="background-color: #fce4ec; color: #880e4f;">
                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th>Detalle</th>
                                    <th>Foto</th>
                                    <th>Brand</th>
                                    <th>Stock</th>
                                    <th>P. Costo</th>
                                    <th>P. Venta</th>
                                    <th>P. Docena</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($productos as $producto)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration + ($productos->currentPage() - 1) * $productos->perPage() }}
                                        </td>
                                        <td>{{ $producto->codigo_producto }}</td>
                                        <td class="text-start">{{ $producto->detalle_producto }}</td>
                                        <td>
                                            @if($producto->foto_producto)
                                                <img src="{{ asset('storage/' . $producto->foto_producto) }}"
                                                    class="rounded-circle object-cover cursor-pointer border shadow-sm"
                                                    style="width: 40px; height: 40px;" alt="Foto" data-bs-toggle="modal"
                                                    data-bs-target="#fotoModal"
                                                    onclick="mostrarImagen('{{ asset('storage/' . $producto->foto_producto) }}')">
                                            @else
                                                <span class="badge bg-light text-dark border">Sin foto</span>
                                            @endif
                                        </td>
                                        <td>{{ $producto->marca->nombre_marca}} </td>
                                        <td>
                                            <span class="badge rounded-pill"
                                                style="background-color: {{ $producto->stock > 0 ? '#d63384' : '#dc3545' }};">
                                                {{ $producto->stock }}
                                            </span>
                                        </td>
                                        <td>{{ $producto->ultimaEntrada?->precio_costo ?? '-' }}</td>
                                        <td>{{ $producto->ultimaEntrada?->precio_venta ?? '-' }}</td>
                                        <td>{{ $producto->ultimaEntrada?->precio_docena ?? '-' }}</td>

                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('productos.show', $producto->id) }}" class="btn btn-sm"
                                                    style="color: #d63384; border-color: #d63384;" title="Ver">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-sm"
                                                    style="color: #198754; border-color: #198754;" title="Editar">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('productos.destroy', $producto->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm"
                                                        style="color: #dc3545; border-color: #dc3545;" title="Eliminar"
                                                        onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted p-5">
                                            <i class="fas fa-box-open fa-3x mb-3" style="color: #f8bbd0;"></i>
                                            <p>No se encontraron productos.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4 d-flex justify-content-end">
                            {{ $productos->links() }}
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