<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Portada de la tienda') }}
        </h2>
    </x-slot>

    @if (session('success'))
        <script>Swal.fire('{{ session("success") }}', '', 'success');</script>
    @endif

    <div class="py-1">
        <div class="container-fluid">

            {{-- ===== Banners ===== --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-4">
                <div class="d-flex justify-content-between align-items-center p-4 border-bottom flex-wrap gap-2">
                    <div>
                        <h3 class="h5 mb-0">Banners principales</h3>
                        <small class="text-muted">Se ven arriba de la portada y cambian solos cada 6 segundos. Tamaño recomendado: 1920 × 640 px.</small>
                    </div>
                    <a href="{{ route('banners.create') }}" class="btn text-white" style="background-color: #d63384;">
                        <i class="fa fa-plus me-2"></i> Nuevo banner
                    </a>
                </div>

                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="text-center" style="background-color: #fce4ec; color: #880e4f;">
                                <tr>
                                    <th style="width:60px">Orden</th>
                                    <th style="width:180px">Imagen</th>
                                    <th>Título y enlace</th>
                                    <th style="width:150px">Fechas</th>
                                    <th style="width:110px">Estado</th>
                                    <th style="width:170px">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($banners as $b)
                                    <tr>
                                        <td class="text-center">{{ $b->orden }}</td>
                                        <td class="text-center">
                                            @if($b->imagen)
                                                <img src="{{ asset('storage/' . $b->imagen) }}" alt="{{ $b->titulo }}"
                                                    style="width:160px; height:54px; object-fit:cover; border-radius:6px;">
                                            @endif
                                            @if($b->imagen_movil)
                                                <div><small class="text-muted">+ imagen para celular</small></div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $b->titulo }}</strong>
                                            @if($b->subtitulo)<div class="small text-muted">{{ $b->subtitulo }}</div>@endif
                                            @if($b->enlace)<div class="small"><i class="fas fa-link me-1"></i>{{ $b->enlace }}</div>@endif
                                        </td>
                                        <td class="small text-center">
                                            @if($b->inicia_en || $b->termina_en)
                                                {{ $b->inicia_en?->format('d/m/Y') ?? 'Desde ya' }}<br>→ {{ $b->termina_en?->format('d/m/Y') ?? 'sin fin' }}
                                            @else
                                                <span class="text-muted">Siempre</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php $color = ['Activo' => 'success', 'Programado' => 'info', 'Vencido' => 'secondary', 'Apagado' => 'secondary'][$b->estado]; @endphp
                                            <span class="badge bg-{{ $color }}">{{ $b->estado }}</span>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('banners.toggle', $b) }}" method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="{{ $b->activo ? 'Apagar' : 'Activar' }}">
                                                    <i class="fa {{ $b->activo ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('banners.edit', $b) }}" class="btn btn-sm btn-success" title="Editar"><i class="fa fa-edit"></i></a>
                                            <form action="{{ route('banners.destroy', $b) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"
                                                    onclick="return confirm('¿Eliminar este banner? Esta acción no se puede deshacer.')"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">
                                        Todavía no hay banners. Mientras tanto la portada muestra un banner de texto sencillo.
                                    </td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ===== Video ===== --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="d-flex justify-content-between align-items-center p-4 border-bottom flex-wrap gap-2">
                    <div>
                        <h3 class="h5 mb-0">Video de la portada</h3>
                        <small class="text-muted">Se muestra el primero que esté activo. MP4 o WebM, sin sonido, de 10 a 20 segundos y menos de 20 MB.</small>
                    </div>
                    <a href="{{ route('banners.create', ['tipo' => 'video']) }}" class="btn text-white" style="background-color: #d63384;">
                        <i class="fa fa-plus me-2"></i> Nuevo video
                    </a>
                </div>

                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="text-center" style="background-color: #fce4ec; color: #880e4f;">
                                <tr>
                                    <th style="width:60px">Orden</th>
                                    <th style="width:180px">Póster</th>
                                    <th>Título</th>
                                    <th style="width:150px">Fechas</th>
                                    <th style="width:110px">Estado</th>
                                    <th style="width:170px">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($videos as $v)
                                    <tr>
                                        <td class="text-center">{{ $v->orden }}</td>
                                        <td class="text-center">
                                            @if($v->imagen)
                                                <img src="{{ asset('storage/' . $v->imagen) }}" alt="{{ $v->titulo }}"
                                                    style="width:160px; height:90px; object-fit:cover; border-radius:6px;">
                                            @else
                                                <span class="text-muted small">Sin póster</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $v->titulo }}</strong>
                                            @if($v->subtitulo)<div class="small text-muted">{{ $v->subtitulo }}</div>@endif
                                        </td>
                                        <td class="small text-center">
                                            @if($v->inicia_en || $v->termina_en)
                                                {{ $v->inicia_en?->format('d/m/Y') ?? 'Desde ya' }}<br>→ {{ $v->termina_en?->format('d/m/Y') ?? 'sin fin' }}
                                            @else
                                                <span class="text-muted">Siempre</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php $color = ['Activo' => 'success', 'Programado' => 'info', 'Vencido' => 'secondary', 'Apagado' => 'secondary'][$v->estado]; @endphp
                                            <span class="badge bg-{{ $color }}">{{ $v->estado }}</span>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('banners.toggle', $v) }}" method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="{{ $v->activo ? 'Apagar' : 'Activar' }}">
                                                    <i class="fa {{ $v->activo ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('banners.edit', $v) }}" class="btn btn-sm btn-success" title="Editar"><i class="fa fa-edit"></i></a>
                                            <form action="{{ route('banners.destroy', $v) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"
                                                    onclick="return confirm('¿Eliminar este video? Esta acción no se puede deshacer.')"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">
                                        Todavía no hay video. La sección no aparece en la portada hasta que subas uno.
                                    </td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <p class="text-muted small mt-3">
                <i class="fas fa-info-circle me-1"></i>
                Los productos en oferta, las novedades y los más vendidos se arman solos con tu catálogo y se actualizan cada pocos minutos.
                Para que un producto salga en "Ofertas" márcalo como oferta en su ficha.
            </p>
        </div>
    </div>
</x-app-layout>
