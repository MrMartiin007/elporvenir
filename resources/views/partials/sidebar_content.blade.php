<!-- Search Widget -->
<div class="sidebar-widget">
    <h4 class="widget-title">Buscar</h4>
    <form action="{{ route('home') }}" method="GET">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar producto..."
                value="{{ $search ?? '' }}">
            @if(request('marca')) <input type="hidden" name="marca" value="{{ request('marca') }}"> @endif
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
        </div>
    </form>
</div>

<!-- Categories/Brands Widget -->
<div class="sidebar-widget">
    <h4 class="widget-title">Marcas</h4>
    <div style="max-height: 400px; overflow-y:auto;">
        <ul class="list-group list-group-flush">
            <li class="list-group-item {{ !($marcaId ?? false) ? 'active' : '' }}">
                <a
                    href="{{ route('home', array_merge(request()->query(), ['marca' => null, 'page' => null])) }}">Todas</a>
            </li>
            @foreach($marcas as $marca)
                <li class="list-group-item {{ ($marcaId ?? null) == $marca->id ? 'active' : '' }}">
                    <!-- Keep search and sort params when switching brands -->
                    <a href="{{ route('home', array_merge(request()->query(), ['marca' => $marca->id, 'page' => null])) }}">
                        {{ $marca->nombre_marca }}
                        <span class="badge-count">{{ $marca->productos_count }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>