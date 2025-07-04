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

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="d-flex justify-content-end flex-column align-items-end px-4 pt-5 gap-2">
                    <a href="{{ route('productos.create') }}" class="btn btn-primary active">
                        <i class="fa fa-plus me-2"></i> Crear Nuevo Producto
                    </a>

                    <div class="col-md-2 offset-md-6 mt-4">
                        <form method="GET" action="{{ route('productos.index') }}">
                            <div class="input-group input-group-sm">
                                <input type="text" name="buscar" value="{{ request('buscar') }}"
                                    class="form-control form-control-sm"
                                    placeholder="Buscar código, detalle o marca...">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="p-6 text-gray-900">
                    <div class="table-responsive-md">
                        <table class="table table-hover table-bordered" id="tablaProductos">
                            <thead class="table-danger text-center text-black">

                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th>Detalle</th>
                                    <th>Foto</th>
                                    <th>Marca</th>
                                    <th>Stock</th>
                                    <th>Precio Costo</th>
                                    <th>Precio Venta</th>
                                    <th>Precio Docena</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($productos as $producto)
                                    <tr class="align-middle text-center">
                                        <td>{{ $loop->iteration + ($productos->currentPage() - 1) * $productos->perPage() }}
                                        </td>
                                        <td class="align-middle text-center">{{ $producto->codigo_producto }}</td>
                                        <td>{{ $producto->detalle_producto }}</td>
                                        <td>
                                            @if($producto->foto_producto)
                                                <img src="{{ asset('storage/' . $producto->foto_producto) }}"
                                                    class="rounded-circle object-cover cursor-pointer"
                                                    style="width: 45px; height: 45px;" alt="Foto producto"
                                                    data-bs-toggle="modal" data-bs-target="#fotoModal"
                                                    onclick="mostrarImagen('{{ asset('storage/' . $producto->foto_producto) }}')">
                                            @else
                                                <span class="text-muted">Sin imagen</span>
                                            @endif

                                        </td>
                                        <td>{{ $producto->marca->nombre_marca}} </td>
                                        <td class="align-middle text-center">{{ $producto->stock }}</td>
                                        <td class="align-middle text-center">
                                            {{ $producto->ultimaEntrada?->precio_costo ?? '-' }}
                                        </td>
                                        <td class="align-middle text-center">
                                            {{ $producto->ultimaEntrada?->precio_venta ?? '-' }}
                                        </td>
                                        <td class="align-middle text-center">
                                            {{ $producto->ultimaEntrada?->precio_docena ?? '-' }}
                                        </td>

                                        <td>
                                            <a href="{{ route('productos.show', $producto->id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('productos.edit', $producto->id) }}"
                                                class="btn btn-sm btn-success">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('productos.destroy', $producto->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No se encontraron resultados para tu
                                            búsqueda.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">
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