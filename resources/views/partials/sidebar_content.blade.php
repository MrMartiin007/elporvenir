
<!-- Categories/Brands Widget -->
<div class="sidebar-widget">
    <h4 class="widget-title">Marcas</h4>

    {{-- Filtro rápido de la lista (no es el buscador del sitio) --}}
    <input type="search" class="form-control form-control-sm marcas-filtro-lateral" autocomplete="off"
        placeholder="Filtrar marcas…" aria-label="Filtrar la lista de marcas" data-filtro-marcas>

    <div style="max-height: 420px; overflow-y:auto;" data-lista-marcas>
        <ul class="list-group list-group-flush">
            <li class="list-group-item {{ !($marcaId ?? false) ? 'active' : '' }}" data-fijo>
                <a
                    href="{{ route('tienda', request()->only('sort')) }}">Todas</a>
            </li>
            @foreach($marcas as $marca)
                <li class="list-group-item {{ ($marcaId ?? null) == $marca->id ? 'active' : '' }}"
                    data-nombre="{{ \Illuminate\Support\Str::ascii(mb_strtolower($marca->nombre_marca)) }}">
                    {{-- Clear search when selecting a brand, keep only sort --}}
                    <a href="{{ $marca->url(request()->only('sort')) }}">
                        <span class="d-flex align-items-center gap-2">
                            <x-marca-logo :marca="$marca" :tam="26" />
                            <span>{{ $marca->nombre_marca }}</span>
                        </span>
                        <span class="badge-count">{{ $marca->productos_count }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <p class="small text-muted px-2 mt-2 mb-0" data-sin-resultados hidden>Ninguna marca coincide.</p>
    </div>

    <a href="{{ route('tienda.marcas') }}" class="d-block small mt-3 text-center">Ver todas las Marcas <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></a>
</div>

@once
<script>
    (function () {
        function normalizar(t) { return t.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').trim(); }
        function iniciar() {
            document.querySelectorAll('[data-filtro-marcas]').forEach(function (campo) {
                var caja = campo.parentElement.querySelector('[data-lista-marcas]');
                if (!caja) return;
                var items = caja.querySelectorAll('li[data-nombre]');
                var vacio = caja.querySelector('[data-sin-resultados]');
                // Si estás en una marca, la lista se abre mostrándola
                var activo = caja.querySelector('li.active[data-nombre]');
                if (activo) caja.scrollTop = Math.max(0, activo.offsetTop - caja.clientHeight / 2 + activo.offsetHeight / 2);
                campo.addEventListener('input', function () {
                    var q = normalizar(campo.value), visibles = 0;
                    items.forEach(function (li) {
                        var oculto = q !== '' && li.getAttribute('data-nombre').indexOf(q) === -1;
                        li.hidden = oculto;
                        if (!oculto) visibles++;
                    });
                    if (vacio) vacio.hidden = visibles !== 0;
                });
            });
        }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', iniciar); else iniciar();
    })();
</script>
@endonce
