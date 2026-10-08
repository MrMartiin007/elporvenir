@extends('layouts.shop')

@section('seo')
    <x-seo-meta title="Marcas de belleza y cosméticos | El Porvenir Puerto Barrios"
        description="Todas las marcas de cosméticos, cuidado del cabello y belleza que encuentras en El Porvenir, Puerto Barrios, Guatemala. Envíos a toda Guatemala."
        :image="asset('logo.jpg')" :url="route('tienda.marcas')" type="website" />
    @php
        $ldMarcas = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Marcas', 'item' => route('tienda.marcas')],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($ldMarcas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endsection

@section('content')
    @php
        $grupos = $marcas->groupBy(function ($m) {
            $letra = mb_strtoupper(mb_substr(\Illuminate\Support\Str::ascii($m->nombre_marca), 0, 1));
            return preg_match('/[A-Z]/', $letra) ? $letra : '#';
        })->sortKeys();
    @endphp

    <div class="container my-4 my-lg-5">
        <nav aria-label="breadcrumb" class="small mb-2">
            <a href="{{ route('home') }}" class="text-decoration-none text-muted">Inicio</a>
            <span class="text-muted mx-1">/</span>
            <span>Marcas</span>
        </nav>
        <h1 class="h3 mb-1">Marcas en El Porvenir, Puerto Barrios</h1>
        <p class="text-muted mb-3">
            {{ $marcas->count() }} marcas de cosméticos, cuidado del cabello y belleza, con envíos a toda Guatemala.
        </p>

        {{-- Filtro rápido y atajo por letra --}}
        <div class="marcas-herramientas mb-4">
            <div class="marcas-filtro-caja">
                <i class="fas fa-search" aria-hidden="true"></i>
                <input type="search" id="filtroMarcas" autocomplete="off" placeholder="Filtrar marcas por nombre…"
                    aria-label="Filtrar marcas por nombre">
            </div>
            <div class="marcas-letras" id="letrasMarcas" aria-label="Ir a la letra">
                @foreach($grupos as $letra => $g)
                    <a href="#letra-{{ $letra === '#' ? 'num' : $letra }}" data-letra="{{ $letra }}">{{ $letra }}</a>
                @endforeach
            </div>
        </div>

        <p id="marcasVacio" class="text-muted" hidden>No encontramos marcas con ese nombre. Prueba con otra palabra.</p>

        @foreach($grupos as $letra => $grupo)
            <section class="marcas-letra" id="letra-{{ $letra === '#' ? 'num' : $letra }}" data-letra="{{ $letra }}" aria-labelledby="titulo-{{ $letra === '#' ? 'num' : $letra }}">
                <h2 id="titulo-{{ $letra === '#' ? 'num' : $letra }}" class="marcas-letra-titulo">{{ $letra }}</h2>
                <ul class="marcas-grid list-unstyled">
                    @foreach($grupo as $m)
                        <li class="marca-item" data-nombre="{{ \Illuminate\Support\Str::ascii(mb_strtolower($m->nombre_marca)) }}">
                            <a href="{{ $m->url() }}" class="marca-card">
                                <x-marca-logo :marca="$m" :tam="84" />
                                <span class="marca-nombre">{{ $m->nombre_marca }}</span>
                                <span class="marca-cuenta">{{ $m->productos_count }} {{ $m->productos_count === 1 ? 'producto' : 'productos' }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var input = document.getElementById('filtroMarcas');
            if (!input) return;
            var items = [].slice.call(document.querySelectorAll('.marca-item'));
            var secciones = [].slice.call(document.querySelectorAll('.marcas-letra'));
            var letras = [].slice.call(document.querySelectorAll('#letrasMarcas a'));
            var vacio = document.getElementById('marcasVacio');

            function normalizar(t) {
                return t.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').trim();
            }

            input.addEventListener('input', function () {
                var q = normalizar(input.value);
                items.forEach(function (li) { li.hidden = q !== '' && li.getAttribute('data-nombre').indexOf(q) === -1; });
                var visibles = 0;
                secciones.forEach(function (s) {
                    var hay = s.querySelectorAll('.marca-item:not([hidden])').length;
                    s.hidden = hay === 0;
                    visibles += hay;
                    letras.forEach(function (a) { if (a.getAttribute('data-letra') === s.getAttribute('data-letra')) a.classList.toggle('apagada', hay === 0); });
                });
                vacio.hidden = visibles !== 0;
            });
        })();
    </script>
@endpush
