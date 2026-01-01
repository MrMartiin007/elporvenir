<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lista de Empresas') }}
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

    <div class="py-9">
        <div class="container-fluid">
            <div class="bg-white shadow-sm sm:rounded-lg">


                <div class="p-4">
                    <a href="{{ route('empresas.create') }}" class="btn text-white" style="background-color: #d63384;">
                        <i class="fas fa-plus"></i> Nueva Empresa
                    </a>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tablaEmpresas">
                            <thead class="text-center" style="background-color: #fce4ec; color: #880e4f;">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre Empresa</th>
                                    <th>Proveedor</th>
                                    <th>Fecha Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($empresas as $empresa)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $empresa->nombre_empresa }}</td>
                                        <td>{{ $empresa->proveedor }}</td>
                                        <td>{{ $empresa->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('empresas.edit', $empresa->id) }}"
                                                class="btn btn-sm btn-outline-success me-1" title="Editar"
                                                style="color: #198754; border-color: #198754;">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('empresas.destroy', $empresa->id) }}" method="POST"
                                                class="d-inline form-eliminar">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                                    style="color: #dc3545; border-color: #dc3545;">
                                                    <i class="fas fa-trash"></i>
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
    </div>

    <script>
        $(document).ready(function () {
            $('#tablaEmpresas').DataTable({
                responsive: true,
                language: {
                    "emptyTable": "No hay empresas registradas",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "search": "Buscar:",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                }
            });

            $('.form-eliminar').submit(function (e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
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