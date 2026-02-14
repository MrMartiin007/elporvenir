<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestión de Ubicaciones
        </h2>
    </x-slot>

    {{-- Notificaciones con SweetAlert2 Toast --}}
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '{{ session('success') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: '{{ session('error') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        </script>
    @endif

    <div class="py-1">
        <div class="container-fluid">
            <div class="bg-white shadow-sm sm:rounded-lg">
                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                    <button type="button" class="btn text-white" style="background-color: #d63384;"
                        data-bs-toggle="modal" data-bs-target="#modalCrearDepartamento">
                        <i class="fa fa-plus me-2"></i> Nuevo Departamento
                    </button>

                    <form method="GET" action="{{ route('ubicaciones.index') }}" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control"
                                placeholder="Buscar...">
                            <button type="submit" class="btn text-white" style="background-color: #d63384;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="p-4">
                    @forelse($departamentos as $departamento)
                        <div class="card mb-3 shadow-sm">
                            {{-- Header del departamento --}}
                            <div class="card-header departamento-header" style="background-color: #fce4ec; cursor: pointer;"
                                data-target="municipios-{{ $departamento->id }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-chevron-right me-2 toggle-icon"></i>
                                        <strong style="color: #880e4f;">{{ $departamento->nombre }}</strong>
                                        <span class="badge bg-secondary ms-2">{{ $departamento->municipios->count() }}
                                            municipios</span>
                                        <span class="badge ms-2 {{ $departamento->activo ? 'bg-success' : 'bg-danger' }}">
                                            {{ $departamento->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </div>
                                    <div class="action-buttons">
                                        {{-- Editar --}}
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                            data-bs-target="#modalEditarDepartamento"
                                            onclick="editarDepartamento({{ $departamento->id }}, '{{ $departamento->nombre }}', {{ $departamento->activo ? 1 : 0 }})">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        {{-- Eliminar --}}
                                        <form action="{{ route('ubicaciones.departamento.destroy', $departamento->id) }}"
                                            method="POST" class="d-inline form-eliminar-departamento">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger btn-eliminar"
                                                data-tipo="departamento" data-nombre="{{ $departamento->nombre }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Body con municipios --}}
                            <div class="municipios-content" id="municipios-{{ $departamento->id }}" style="display: none;">
                                <div class="card-body">
                                    @if($departamento->municipios->count() > 0)
                                        <table class="table table-sm table-striped">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="color: #880e4f;">Municipio</th>
                                                    <th style="color: #880e4f;">Estado</th>
                                                    <th style="color: #880e4f;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($departamento->municipios as $municipio)
                                                    <tr>
                                                        <td>{{ $municipio->nombre }}</td>
                                                        <td>
                                                            <span
                                                                class="badge {{ $municipio->activo ? 'bg-success' : 'bg-danger' }}">
                                                                {{ $municipio->activo ? 'Activo' : 'Inactivo' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-success"
                                                                data-bs-toggle="modal" data-bs-target="#modalEditarMunicipio"
                                                                onclick="editarMunicipio({{ $municipio->id }}, '{{ $municipio->nombre }}', {{ $departamento->id }}, {{ $municipio->activo ? 1 : 0 }})">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                            <form
                                                                action="{{ route('ubicaciones.municipio.destroy', $municipio->id) }}"
                                                                method="POST" class="d-inline form-eliminar-municipio">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="btn btn-sm btn-danger btn-eliminar"
                                                                    data-tipo="municipio" data-nombre="{{ $municipio->nombre }}">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p class="text-muted mb-3">No hay municipios registrados</p>
                                    @endif

                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalCrearMunicipio" data-departamento-id="{{ $departamento->id }}"
                                        data-departamento-nombre="{{ $departamento->nombre }}">
                                        <i class="fa fa-plus"></i> Agregar Municipio
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted py-5">No hay departamentos registrados</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- MODALES --}}
    @include('ubicacion.modals.crear-departamento')
    @include('ubicacion.modals.editar-departamento')
    @include('ubicacion.modals.crear-municipio')
    @include('ubicacion.modals.editar-municipio')

    {{-- JavaScript --}}
    <script>
        $(document).ready(function () {
            console.log('Módulo de ubicaciones cargado');

            // Acordeón manual con jQuery
            $('.departamento-header').on('click', function (e) {
                if ($(e.target).closest('.action-buttons').length > 0) {
                    return;
                }

                const targetId = $(this).data('target');
                const content = $('#' + targetId);
                const icon = $(this).find('.toggle-icon');

                content.slideToggle(300, function () {
                    if (content.is(':visible')) {
                        icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
                    } else {
                        icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
                    }
                });
            });

            // Pre-cargar departamento en modal crear municipio
            $('#modalCrearMunicipio').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const deptoId = button.data('departamento-id');
                const deptoNombre = button.data('departamento-nombre');

                if (deptoId && deptoNombre) {
                    $('#departamento_id').val(deptoId);
                    $('#departamento_nombre_display').text(deptoNombre);
                    console.log('Departamento pre-seleccionado:', deptoNombre, 'ID:', deptoId);
                }
            });

            // Confirmación de eliminación con SweetAlert2
            $('.btn-eliminar').on('click', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');
                const tipo = $(this).data('tipo');
                const nombre = $(this).data('nombre');

                Swal.fire({
                    title: '¿Estás seguro?',
                    html: `Se eliminará el ${tipo} <strong>${nombre}</strong>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d63384',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fa fa-trash me-1"></i> Sí, eliminar',
                    cancelButtonText: '<i class="fa fa-times me-1"></i> Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Debug: monitorear envío de formularios
            $('form').on('submit', function (e) {
                const formData = $(this).serialize();
                const action = $(this).attr('action');
                console.log('📤 Enviando formulario a:', action);
                console.log('📦 Datos:', formData);
            });

            // Reabrir modal si hay errores de validación
            @if($errors->any())
                console.log('❌ Errores de validación:', @json($errors->all()));

                @if($errors->has('departamento_id') || (old('departamento_id') && $errors->has('nombre')))
                    // Error en municipio
                    $('#modalCrearMunicipio').modal('show');
                    console.log('Reabriendo modal de municipio');
                @elseif($errors->has('nombre'))
                    // Error en departamento
                    $('#modalCrearDepartamento').modal('show');
                    console.log('Reabriendo modal de departamento');
                @endif
            @endif
        });

        function editarDepartamento(id, nombre, activo) {
            document.querySelector('#modalEditarDepartamento form').action = `/admin/ubicaciones/departamento/${id}`;
            document.getElementById('edit_nombre_depto').value = nombre;
            document.getElementById('edit_activo_depto').checked = activo == 1;
        }

        function editarMunicipio(id, nombre, departamentoId, activo) {
            document.querySelector('#modalEditarMunicipio form').action = `/admin/ubicaciones/municipio/${id}`;
            document.getElementById('edit_nombre_muni').value = nombre;
            document.getElementById('edit_departamento_muni').value = departamentoId;
            document.getElementById('edit_activo_muni').checked = activo == 1;
        }
    </script>
</x-app-layout>