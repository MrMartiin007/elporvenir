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

    <div class="py-10">
        <div class="container-fluid">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                    <a href="{{ route('marcas.create') }}" class="btn text-white" style="background-color: #d63384;">
                        <i class="fa fa-plus me-2"></i> Crear Nueva Marca
                    </a>

                    <form method="GET" action="{{ route('marcas.index') }}" class="d-flex">
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
                        <table class="table table-hover table-bordered align-middle" id="tablaMarcas">
                            <thead class="text-center" style="background-color: #fce4ec; color: #880e4f;">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre Marca</th>
                                    <th>Logo Marca</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($marcas as $marca)
                                    <tr class="align-middle text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $marca->nombre_marca }}</td>
                                        <td>
                                            @if ($marca->foto_marca)
                                                <img src="{{ asset('storage/' . $marca->foto_marca) }}"
                                                    class="rounded-circle object-cover" style="width: 45px; height: 45px;"
                                                    alt="Logo Marca">
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
                                            <form action="{{ route('marcas.destroy', $marca->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('¿Estás seguro de eliminar esta marca?')">
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
                            {{ $marcas->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>