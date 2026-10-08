{{--
    Encabezado de la tienda (compartido por todas las páginas públicas). Diseño "buscador protagonista":
      fila 1: logo · buscador con sugerencias · WhatsApp y carrito
      fila 2: menú · atajos a las marcas más vendidas
    Se queda fijo al bajar y se encoge para ocupar poco. El logo ya trae el nombre completo.
    En celular el buscador va en una franja aparte debajo (para que la barra fija sea chica).
--}}
@php
    $cartCount = collect(session('carrito', []))->sum('cantidad');
    $enlaces = [
        ['Inicio', route('home'), request()->routeIs('home')],
        ['Tienda', route('tienda'), request()->routeIs('tienda', 'marca.show', 'producto.show')],
        ['Marcas', route('tienda.marcas'), request()->routeIs('tienda.marcas')],
        ['Contacto', route('contact'), request()->routeIs('contact')],
    ];
    $populares = \App\Models\Marca::populares(4);
@endphp

<header class="cab3" id="cabecera">
    <div class="container cab3-fila1">
        <button class="cab3-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal"
            aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menú">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>

        <a href="{{ route('home') }}" class="cab3-logo" aria-label="El Porvenir Beauty Center, ir al inicio">
            <img src="{{ asset('logo-cabecera.jpg') }}" alt="El Porvenir Beauty Center" width="723" height="437" fetchpriority="high">
        </a>

        <div class="cab3-buscador d-none d-lg-block">
            @include('partials.buscador', ['variante' => 'grande'])
        </div>

        <div class="cab3-acciones">
            <a href="https://wa.me/50238995635" target="_blank" rel="noopener" class="cab3-wa d-none d-lg-inline-flex" title="Escríbenos por WhatsApp">
                <i class="fab fa-whatsapp" aria-hidden="true"></i>
                <span><small>Escríbenos</small><strong>3899 5635</strong></span>
            </a>
            <a class="cab3-carrito position-relative" href="{{ route('cart.index') }}" title="Ver Carrito">
                <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                <span class="js-cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill {{ $cartCount > 0 ? '' : 'd-none' }}">{{ $cartCount }}</span>
            </a>
        </div>
    </div>

    <div class="collapse cab3-colapso" id="menuPrincipal">
        <nav class="cab3-menu" aria-label="Menú principal">
            <div class="container cab3-menu-fila">
                <ul class="cab3-links">
                    @foreach($enlaces as [$texto, $url, $activo])
                        <li><a href="{{ $url }}" class="{{ $activo ? 'activo' : '' }}" @if($activo) aria-current="page" @endif>{{ $texto }}</a></li>
                    @endforeach
                </ul>

                @if($populares->isNotEmpty())
                    <div class="cab3-chips d-none d-lg-flex">
                        <span>Marcas populares</span>
                        @foreach($populares as $m)
                            <a href="{{ $m->url() }}" class="cab3-chip">{{ $m->nombre_marca }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        </nav>
    </div>
</header>

{{-- Celular: el buscador va debajo de la barra fija --}}
<div class="cab3-buscador-movil d-lg-none">
    <div class="container">
        @include('partials.buscador', ['variante' => 'grande', 'placeholder' => 'Buscar productos o marcas…'])
    </div>
</div>

@once
<script>
    // Al bajar, el encabezado se encoge (logo más chico y sin atajos) para ocupar poco espacio.
    (function () {
        var cab = document.getElementById('cabecera');
        if (!cab) return;
        var ocupado = false;
        function revisar() {
            ocupado = false;
            // Dos umbrales distintos: encoger cambia la altura y puede mover el scroll; así no parpadea.
            var y = window.scrollY;
            if (y > 100) cab.classList.add('mini');
            else if (y < 30) cab.classList.remove('mini');
        }
        window.addEventListener('scroll', function () {
            if (!ocupado) { ocupado = true; requestAnimationFrame(revisar); }
        }, { passive: true });
        revisar();
    })();
</script>
@endonce
