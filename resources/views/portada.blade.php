@extends('layouts.shop')

@section('seo')
    <x-seo-meta title="El Porvenir Guatemala | Beauty Center en Puerto Barrios"
        description="El Porvenir, tienda de cosméticos y Beauty Center en Puerto Barrios, Guatemala. Maquillaje, skincare, perfumes y cuidado personal con envíos a toda Guatemala."
        keywords="cosméticos, belleza, cuidado personal, maquillaje, skincare, perfumes, Puerto Barrios, Izabal, Guatemala, productos de belleza, beauty center"
        :image="$banners->first()?->imagen ? asset('storage/' . $banners->first()->imagen) : asset('logo.jpg')"
        :url="route('home')" type="website" />
    @include('partials.seo-negocio')
@endsection

@push('head')
    @vite(['resources/css/shop-portada.css'])
@endpush

@section('content')
    <a href="https://wa.me/50238995635" class="whatsapp-float" target="_blank" rel="noopener" title="Contáctanos por WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>


    {{-- ===== 1. Banner principal ===== --}}
    <section class="container-xl mt-3" aria-label="Promociones">
        @if($banners->isNotEmpty())
            @php $heroMovil = $banners->every(fn ($b) => filled($b->imagen_movil)); @endphp
            <div id="heroPortada" class="carousel slide hero {{ $heroMovil ? 'hero--movil' : '' }}"
                @if($banners->count() > 1) data-bs-ride="carousel" data-bs-interval="6000" data-bs-pause="hover" @endif>
                @if($banners->count() > 1)
                    <div class="carousel-indicators">
                        @foreach($banners as $i => $b)
                            <button type="button" data-bs-target="#heroPortada" data-bs-slide-to="{{ $i }}"
                                class="{{ $i === 0 ? 'active' : '' }}" @if($i === 0) aria-current="true" @endif
                                aria-label="Banner {{ $i + 1 }}"></button>
                        @endforeach
                    </div>
                @endif

                <div class="carousel-inner h-100">
                    @foreach($banners as $i => $b)
                        @php
                            $srcDesktop = \App\Services\ImageVariants::srcset($b->imagen, \App\Services\ImageVariants::BANNER_WIDTHS);
                            $srcMovil = ($heroMovil && $b->imagen_movil) ? \App\Services\ImageVariants::srcset($b->imagen_movil, \App\Services\ImageVariants::BANNER_MOBILE_WIDTHS) : null;
                            $conTexto = filled($b->subtitulo) || filled($b->texto_boton);
                        @endphp
                        <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                            @if($b->enlace_url)<a href="{{ $b->enlace_url }}" class="hero-link">@endif
                                <picture>
                                    @if($heroMovil && $srcMovil)
                                        <source media="(max-width: 767px)" type="image/webp" srcset="{{ $srcMovil }}" sizes="100vw">
                                    @endif
                                    @if($heroMovil)
                                        <source media="(max-width: 767px)" srcset="{{ asset('storage/' . $b->imagen_movil) }}">
                                    @endif
                                    @if($srcDesktop)
                                        <source type="image/webp" srcset="{{ $srcDesktop }}" sizes="(min-width: 1320px) 1296px, 100vw">
                                    @endif
                                    <img src="{{ asset('storage/' . $b->imagen) }}" alt="{{ $b->titulo }}" class="hero-img"
                                        width="1920" height="640" decoding="async"
                                        @if($i === 0) fetchpriority="high" loading="eager" @else loading="lazy" @endif>
                                </picture>
                                @if($conTexto)
                                    <div class="hero-caption">
                                        <p class="hero-title">{{ $b->titulo }}</p>
                                        @if($b->subtitulo)<p class="hero-sub">{{ $b->subtitulo }}</p>@endif
                                        @if($b->texto_boton)<span class="hero-btn">{{ $b->texto_boton }}</span>@endif
                                    </div>
                                @endif
                            @if($b->enlace_url)</a>@endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            {{-- Mientras no haya banners cargados desde el admin --}}
            <div class="hero hero--default">
                <div>
                    <p class="hero-title mb-1" style="font-family:'Playfair Display',serif;font-size:clamp(1.5rem,4vw,2.6rem);font-weight:700;">Belleza y cuidado personal</p>
                    <p class="mb-3 text-muted">Maquillaje, cuidado del cabello, skincare y perfumes. Envíos a toda Guatemala.</p>
                    <a href="{{ route('tienda') }}" class="btn btn-theme px-4">Ver la tienda</a>
                </div>
            </div>
        @endif
    </section>

    {{-- ===== Titular, confianza ===== --}}
    <section class="container-xl">
        <h1 class="portada-h1">El Porvenir: cosméticos y cuidado personal en Puerto Barrios, Guatemala</h1>
        <p class="portada-lead">Maquillaje, skincare, cuidado del cabello y perfumes de las mejores marcas, con envíos a toda Guatemala.</p>
        <div class="trust-row">
            <div class="trust-item"><i class="fas fa-shipping-fast" aria-hidden="true"></i> Envíos a toda Guatemala</div>
            <div class="trust-item"><i class="fas fa-shield-alt" aria-hidden="true"></i> Productos 100% originales</div>
            <div class="trust-item"><i class="fas fa-store" aria-hidden="true"></i> Tienda física en Puerto Barrios</div>
        </div>
    </section>

    {{-- ===== 2. Marcas ===== --}}
    @if($marcasLogo->isNotEmpty())
        <section class="container-xl portada-section" aria-labelledby="sec-marcas">
            <div class="section-head">
                <h2 id="sec-marcas">Compra por marca</h2>
                <a href="{{ route('tienda.marcas') }}">Ver todas <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></a>
            </div>
            <div class="brand-strip">
                @foreach($marcasLogo as $m)
                    <a href="{{ $m->url() }}" class="brand-chip" title="{{ $m->nombre_marca }}">
                        <x-marca-logo :marca="$m" :tam="72" class="d-block mx-auto mb-1" />
                        <span>{{ $m->nombre_marca }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ===== 3. Ofertas ===== --}}
    @include('partials.portada-seccion', ['titulo' => 'Ofertas', 'productos' => $ofertas, 'enlace' => route('tienda', ['sort' => 'newest']), 'textoEnlace' => 'Ver la tienda'])

    {{-- ===== 4. Video (solo si hay uno activo) ===== --}}
    @if($video && $video->video)
        <section class="container-xl portada-section" aria-labelledby="sec-video">
            <div class="section-head">
                <h2 id="sec-video">{{ $video->titulo }}</h2>
                @if($video->enlace_url)
                    <a href="{{ $video->enlace_url }}">{{ $video->texto_boton ?: 'Ver más' }} <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></a>
                @endif
            </div>
            @if($video->subtitulo)<p class="text-muted mb-2">{{ $video->subtitulo }}</p>@endif
            <div class="video-wrap" data-video-wrap>
                <video muted loop playsinline preload="none" aria-label="{{ $video->titulo }}"
                    @if($video->imagen) poster="{{ asset('storage/' . $video->imagen) }}" @endif
                    data-src="{{ asset('storage/' . $video->video) }}"></video>
                <button type="button" class="video-play" aria-label="Reproducir video"><i class="fas fa-play" aria-hidden="true"></i></button>
            </div>
        </section>
    @endif

    {{-- ===== 5. Novedades y más vendidos ===== --}}
    @include('partials.portada-seccion', ['titulo' => 'Novedades', 'productos' => $novedades, 'enlace' => route('tienda', ['sort' => 'newest']), 'textoEnlace' => 'Ver toda la tienda'])
    @include('partials.portada-seccion', ['titulo' => 'Los más vendidos', 'productos' => $masVendidos, 'enlace' => route('tienda'), 'textoEnlace' => 'Ver toda la tienda'])

    <div class="container-xl text-center mt-4">
        <a href="{{ route('tienda') }}" class="btn btn-theme px-5 py-2">Ver todos los productos</a>
    </div>

    {{-- ===== 6. Sobre el negocio ===== --}}
    <section class="container-xl portada-section" aria-labelledby="sobre-el-porvenir">
        <div class="about-box">
            <h2 id="sobre-el-porvenir" class="h4 mb-3">Sobre El Porvenir Beauty Center</h2>
            <p class="mb-2">
                El Porvenir es una tienda de cosméticos y beauty center en Puerto Barrios, Izabal, Guatemala.
                Encuentras maquillaje, cuidado de la piel, perfumes y productos de cuidado personal de
                {{ $marcasTotal ?? $marcasLogo->count() }} marcas, y puedes comprarlos en línea con envío a toda Guatemala o visitarnos
                en nuestra tienda física.
            </p>
            <p class="mb-0 text-muted small">
                Escríbenos por
                <a href="https://wa.me/50238995635" target="_blank" rel="noopener">WhatsApp al 3899-5635</a>
                o <a href="{{ route('contact') }}">conoce cómo contactarnos</a>.
            </p>
        </div>
    </section>
@endsection

@push('scripts')
    @include('partials.cart-ajax')
    <script>
        // Las diapositivas del banner (menos la primera) se descargan cuando la página ya terminó de cargar,
        // para que al rotar no aparezca un hueco, sin restarle velocidad a la carga inicial.
        window.addEventListener('load', function () {
            var precargar = function () {
                document.querySelectorAll('#heroPortada img[loading="lazy"]').forEach(function (i) { i.loading = 'eager'; });
            };
            ('requestIdleCallback' in window) ? requestIdleCallback(precargar, { timeout: 2500 }) : setTimeout(precargar, 1200);
        });
    </script>
    <script>
        (function () {
            var wrap = document.querySelector('[data-video-wrap]');
            if (!wrap) return;
            var video = wrap.querySelector('video');
            var btn = wrap.querySelector('.video-play');
            var ahorro = window.matchMedia('(prefers-reduced-motion: reduce)').matches ||
                (navigator.connection && navigator.connection.saveData);

            function cargar() { if (!video.src) video.src = video.getAttribute('data-src'); }
            function pedirToque() { wrap.classList.add('needs-tap'); }

            btn.addEventListener('click', function () {
                cargar();
                video.muted = false;
                video.controls = true;
                wrap.classList.remove('needs-tap');
                video.play().catch(pedirToque);
            });

            if (ahorro || !('IntersectionObserver' in window)) { pedirToque(); return; }

            // El video solo se descarga y reproduce cuando se ve en pantalla, y se pausa al salir.
            new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) { cargar(); video.play().catch(pedirToque); }
                    else { video.pause(); }
                });
            }, { threshold: 0.35 }).observe(wrap);
        })();
    </script>
@endpush
