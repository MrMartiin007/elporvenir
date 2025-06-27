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
                <div class="row align-items-end">
                    <div class="col text-end mt-4 pe-5">
                        <a href="{{ route('productos.create') }}" class="btn btn-primary active">
                            <i class="fa fa-plus me-2"></i> Crear Nuevo Producto
                        </a>
                    </div>
                </div>

                <div class="p-6 text-gray-900">
                    <div class="table-responsive-md">
                        <table class="table table-hover table-bordered" id="tablaProductos">
                            <thead class="text-center" style="background-color: #ffb6c1; color: #000;">
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
                                @foreach($productos as $producto)
                                    <tr class="align-middle text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="align-middle text-center">{{ $producto->codigo_producto }}</td>
                                        <td>{{ $producto->detalle_producto }}</td>
                                        <td>
                                            @if($producto->foto_producto)
                                                <img src="{{ asset('storage/' . $producto->foto_producto) }}"
                                                    class="rounded-circle object-cover" style="width: 45px; height: 45px;"
                                                    alt="Foto producto">
                                            @else
                                                <span class="text-muted">Sin imagen</span>
                                            @endif
                                        </td>
                                        <td>{{ $producto->marca->nombre_marca}} </td>
                                        <td class="align-middle text-center">{{ $producto->stock }}</td>
                                        <td class="align-middle text-center">{{ $producto->ultimaEntrada?->precio_costo ?? '-' }}</td>
                                        <td class="align-middle text-center">{{ $producto->ultimaEntrada?->precio_venta ?? '-' }}</td>
                                        <td class="align-middle text-center">{{ $producto->ultimaEntrada?->precio_docena ?? '-' }}</td>

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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

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