<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nueva Venta') }}
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
                        <a href="{{ route('ventas.nueva') }}" class="btn btn-primary active">
                            <i class="fa fa-plus me-2"></i> Crear Nueva Venta
                        </a>

                    </div>
                </div>

                <div class="p-6 text-gray-900">
                    <div class="table-responsive-md">
                        <table class="table table-hover table-bordered" id="tablaProductos">
                            <thead class="text-center" style="background-color: #ffb6c1; color: #000;">
                                <tr>
                                    <th>No</th>
                                    <th>Fecha Venta</th>
                                    <th>Cantidad Productos</th>
                                    <th>Total Vendido</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ventas as $venta)
                                    <tr class="align-middle text-center">
                                        <td>{{ ++$i }}</td>
                                        <td>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d-m-Y') }}</td>
                                        <td class="align-middle text-center">{{ $venta->cantidad_productos }}</td>
                                        <td class="align-middle text-center">{{ $venta->total_vendido }}</td>
                                        <td>
                                            <a href="{{ route('ventas.show', $venta->id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>

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