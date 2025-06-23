<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lista de marcas registradas') }}
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

  <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 bg-white shadow-sm sm:rounded-lg p-4">
                <div class="row align-items-end">
                    <div class="col text-end mt-4 pe-5">
                        <a href="{{ route('marcas.create') }}" class="btn btn-primary active">
                            <i class="fa fa-plus me-2"></i> Crear Nueva Marca
                        </a>
                    </div>
                </div>

                <div class="p-6 text-gray-900">
                    <div class="table-responsive-md">
                        <table class="table table-hover table-bordered" id="tablaMarcas">
                            <thead class="text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre Marca</th>
                                    <th>Logo Marca</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($marcas as $marca)
                                    <tr class="align-middle text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $marca->nombre_marca }}</td>
                                        <td>
                                            @if ($marca->logo_marca)
                                                <img src="{{ asset('storage/' . $marca->logo_marca) }}" class="rounded-circle object-cover" style="width: 45px; height: 45px;" alt="Logo Marca">
                                            @else
                                                <span class="text-muted">Sin logo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('marcas.show', $marca->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('marcas.edit', $marca->id) }}" class="btn btn-sm btn-success">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('marcas.destroy', $marca->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta marca?')">
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
        let table = $('#tablaMarcas').DataTable({
            responsive: true,
            scrollX: true,
            autoWidth: false,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            language: {
                "emptyTable": "No hay marcas registradas",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ marcas",
                "infoEmpty": "Mostrando 0 a 0 de 0 marcas",
                "infoFiltered": "(filtrado de _MAX_ marcas)",
                "lengthMenu": "Mostrar _MENU_ marcas",
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
