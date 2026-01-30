<!-- Search Widget -->
<div class="sidebar-widget">
    <h4 class="widget-title">Buscar</h4>
    <form action="{{ route('home') }}" method="GET">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar producto..."
                value="{{ $search ?? '' }}">
            {{-- Sort is maintained, but marca filter is cleared when searching --}}
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
                    href="{{ route('home', array_merge(request()->except(['marca', 'page', 'search']), ['marca' => null])) }}">Todas</a>
            </li>
            @foreach($marcas as $marca)
                <li class="list-group-item {{ ($marcaId ?? null) == $marca->id ? 'active' : '' }}">
                    {{-- Clear search when selecting a brand, keep only sort --}}
                    <a href="{{ route('home', array_merge(request()->only('sort'), ['marca' => $marca->id])) }}">
                        {{ $marca->nombre_marca }}
                        <span class="badge-count">{{ $marca->productos_count }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>