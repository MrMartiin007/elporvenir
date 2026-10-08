<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- SEO Meta Tags --}}
    @php
        // Canonical propio por página: marca y paginación se indexan; búsquedas y orden no.
        $seoPage = $productos->currentPage();
        $seoMarca = $marcaActual?->nombre_marca;
        // URL de una página del listado (inicio o marca) sin parámetros de búsqueda ni orden
        $seoPageUrl = fn ($page) => $marcaActual
            ? $marcaActual->url(['page' => $page > 1 ? $page : null])
            : route('tienda', array_filter(['page' => $page > 1 ? $page : null]));
        $seoUrl = $seoPageUrl($seoPage);
        $seoNoIndex = filled($search) || filled($sort) || (filled($marcaId) && !$marcaActual);

        // Títulos: "El Porvenir" siempre al inicio o al final, junto a la ciudad, que es como la gente lo busca.
        $seoTitle = 'Tienda en línea de cosméticos y belleza | El Porvenir Puerto Barrios';
        if ($seoMarca) {
            $seoTitle = $seoMarca . ' en Puerto Barrios | El Porvenir Guatemala';
        }
        if ($seoPage > 1) {
            $seoTitle .= ' - Página ' . $seoPage;
        }
        $seoDescription = $seoMarca
            ? 'Compra ' . $productos->total() . ' productos ' . $seoMarca . ' en El Porvenir, Puerto Barrios, Guatemala. Envíos a toda Guatemala.'
            : 'Compra en línea maquillaje, skincare, perfumes y cuidado personal en El Porvenir, Puerto Barrios, Guatemala. Más de ' . (floor($productos->total() / 100) * 100) . ' productos con envíos a toda Guatemala.';
        if ($seoMarca && \App\Services\ImageVariants::existe($marcaActual->foto_marca)) {
            $seoImage = asset('storage/' . $marcaActual->foto_marca);
        } else {
            $seoImage = asset('logo.jpg');
        }
    @endphp
    <x-seo-meta :title="$seoTitle"
        :description="$seoDescription"
        keywords="cosméticos, belleza, cuidado personal, maquillaje, skincare, perfumes, Puerto Barrios, Izabal, Guatemala, productos de belleza, beauty center"
        :image="$seoImage" :url="$seoUrl" type="website"
        :robots="$seoNoIndex ? 'noindex, follow' : 'index, follow'" />
    @if($productos->previousPageUrl())
        <link rel="prev" href="{{ $seoPageUrl($seoPage - 1) }}">
    @endif
    @if($productos->hasMorePages())
        <link rel="next" href="{{ $seoPageUrl($seoPage + 1) }}">
    @endif

    {{-- Datos estructurados: lista de productos y migas de pan (solo páginas indexables) --}}
    @unless($seoNoIndex)
        @php
            $ldLista = [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $seoTitle,
                'description' => $seoDescription,
                'url' => $seoUrl,
                'isPartOf' => ['@type' => 'WebSite', 'name' => 'El Porvenir Beauty Center', 'url' => route('home')],
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'numberOfItems' => $productos->total(),
                    'itemListElement' => $productos->values()->map(fn ($p, $i) => [
                        '@type' => 'ListItem',
                        'position' => ($productos->currentPage() - 1) * $productos->perPage() + $i + 1,
                        'url' => $p->url,
                        'name' => $p->detalle_producto,
                    ])->all(),
                ],
            ];
            $ldMigas = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => array_values(array_filter([
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => route('home')],
                    $marcaActual ? ['@type' => 'ListItem', 'position' => 2, 'name' => $seoMarca, 'item' => $marcaActual->url()] : null,
                ])),
            ];
            $ldFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP;
        @endphp
        <script type="application/ld+json">{!! json_encode($ldLista, $ldFlags) !!}</script>
        @if($marcaActual)
            <script type="application/ld+json">{!! json_encode($ldMigas, $ldFlags) !!}</script>
        @endif
    @endunless

    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/shop-home.css', 'resources/css/shop-buscador.css', 'resources/css/shop-tema.css'])
</head>

<body>
    @include('partials.aviso-superior')

    <!-- WhatsApp Button -->
    <a href="https://wa.me/50238995635" class="whatsapp-float" target="_blank" title="Contáctanos por WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Navbar -->
    @include('partials.cabecera')

    <!-- Content -->
    <div class="container mt-3 mt-lg-5 mb-5">
        <div class="row">

            <!-- Sidebar (Desktop: Static | Mobile: Offcanvas) -->
            <div class="col-lg-3 order-2 order-lg-1 d-none d-lg-block">
                <!-- Desktop Sidebar Content (duplicated for simplicity or use partials in real app) -->
                @include('partials.sidebar_content')
            </div>

            <!-- Product Grid -->
            <div class="col-lg-9 order-1 order-lg-2">

                <!-- Mobile: Sticky Search & Brand Nav -->
                <div class="d-block d-lg-none mb-4">

                    <!-- Horizontal Brand Pills -->
                    <!-- Horizontal Brand Pills -->
                    <div class="horizontal-scroll d-flex gap-2 pb-2 align-items-center ps-1">
                        <!-- 'Todas' Pill -->
                        <a href="{{ route('tienda', request()->only('sort')) }}"
                            class="brand-pill {{ !($marcaId ?? false) ? 'active' : '' }} d-flex align-items-center justify-content-center border shadow-sm"
                            style="width: 50px; height: 50px; min-width: 50px; padding: 0; border-radius: 50%;">
                            <span class="small fw-bold">Todas</span>
                        </a>
                        @php
                            // Solo las 40 marcas con más productos (la lista completa está en /marcas);
                            // la marca activa siempre se muestra.
                            $marcasPills = $marcas->sortByDesc('productos_count')->take(40);
                            if ($marcaActual && !$marcasPills->contains('id', $marcaActual->id)) {
                                $marcasPills->prepend($marcas->firstWhere('id', $marcaActual->id));
                            }
                        @endphp
                        @foreach($marcasPills as $marca)
                            {{-- Clear search when selecting brand, keep only sort --}}
                            <a href="{{ $marca->url(request()->only('sort')) }}"
                                class="brand-pill {{ ($marcaId ?? null) == $marca->id ? 'active' : '' }} p-0 d-flex align-items-center justify-content-center border shadow-sm"
                                title="{{ $marca->nombre_marca }}"
                                style="width: 50px; height: 50px; min-width: 50px; border-radius: 50%; overflow: hidden;">
                                @if(\App\Services\ImageVariants::existe($marca->foto_marca))
                                    <img src="{{ \App\Services\ImageVariants::url($marca->foto_marca, 200) }}"
                                        alt="{{ $marca->nombre_marca }}" width="50" height="50" loading="lazy"
                                        decoding="async" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <span class="small fw-bold text-uppercase">{{ substr($marca->nombre_marca, 0, 2) }}</span>
                                @endif
                            </a>
                        @endforeach
                        <a href="{{ route('tienda.marcas') }}"
                            class="brand-pill d-flex align-items-center justify-content-center border shadow-sm text-center"
                            title="Ver todas las marcas"
                            style="width: 50px; height: 50px; min-width: 50px; padding: 0; border-radius: 50%;">
                            <span class="small fw-bold" style="font-size: .62rem; line-height: 1.1;">Ver<br>todas</span>
                        </a>
                    </div>
                </div>


                {{-- Encabezado de la página (el <h1> que Google usa para entender el tema) --}}
                <header class="mb-3">
                    @if($marcaActual)
                        <nav aria-label="breadcrumb" class="small mb-1">
                            <a href="{{ route('home') }}" class="text-decoration-none text-muted">Inicio</a>
                            <span class="text-muted mx-1">/</span>
                            <span>{{ $marcaActual->nombre_marca }}</span>
                        </nav>
                        <div class="d-flex align-items-center gap-3">
                            <x-marca-logo :marca="$marcaActual" :tam="72" :alt="$marcaActual->nombre_marca" />
                            <div>
                                <h1 class="h3 mb-1">{{ $marcaActual->nombre_marca }} en Puerto Barrios</h1>
                                <p class="text-muted small mb-0">
                                    {{ $productos->total() }} {{ $productos->total() === 1 ? 'producto' : 'productos' }}
                                    de {{ $marcaActual->nombre_marca }} en El Porvenir Beauty Center. Envíos a toda Guatemala.
                                </p>
                            </div>
                        </div>
                    @elseif(filled($search))
                        <h1 class="h3 mb-0">Resultados para «{{ $search }}»</h1>
                    @else
                        {{-- Título de la página solo para buscadores y lectores de pantalla (no se ve) --}}
                        <h1 class="visually-hidden">Tienda en línea: cosméticos y cuidado personal en Guatemala</h1>
                    @endif
                </header>

                <!-- Sort/Filter Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <p class="mb-0 text-muted small text-nowrap">Resultados: {{ $productos->total() }}</p>

                    <div class="d-flex align-items-center gap-2">
                        <!-- Mobile Filter Toggle (Hidden as we moved filters to main view) -->
                        <!-- <button class="btn btn-outline-dark btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                            <i class="fas fa-filter"></i> Filtros
                        </button> -->

                        <form id="sortForm" action="{{ route('tienda') }}" method="GET"
                            class="d-flex align-items-center mb-0">
                            @foreach(request()->except(['sort', 'page']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach

                            <select name="sort" class="form-select form-select-sm"
                                onchange="document.getElementById('sortForm').submit()" aria-label="Ordenar">
                                <option value="newest" {{ ($sort ?? '') == 'newest' ? 'selected' : '' }}>Lo más nuevo
                                </option>
                                <option value="price_asc" {{ ($sort ?? '') == 'price_asc' ? 'selected' : '' }}>Precio:
                                    Menor a Mayor</option>
                                <option value="price_desc" {{ ($sort ?? '') == 'price_desc' ? 'selected' : '' }}>Precio:
                                    Mayor a Menor</option>
                                <option value="oldest" {{ ($sort ?? '') == 'oldest' ? 'selected' : '' }}>Lo más antiguo
                                </option>
                            </select>
                        </form>
                    </div>
                </div>

                @if($productos->count() > 0)
                    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4">
                        @foreach($productos as $producto)
                            <div class="col">
                                <div class="card card-product" itemscope itemtype="https://schema.org/Product">
                                    {{-- Schema.org Product Data --}}
                                    <meta itemprop="name" content="{{ $producto->detalle_producto }}">
                                    <meta itemprop="brand" content="{{ $producto->marca->nombre_marca ?? 'El Porvenir' }}">
                                    @if($producto->ultimaEntrada && $producto->ultimaEntrada->precio_venta)
                                        <meta itemprop="price" content="{{ $producto->ultimaEntrada->precio_venta }}">
                                        <meta itemprop="priceCurrency" content="GTQ">
                                    @endif
                                    <meta itemprop="description" content="{{ $producto->detalle_producto }} de {{ $producto->marca->nombre_marca ?? 'El Porvenir' }}">
                                    <link itemprop="availability"
                                        href="https://schema.org/{{ $producto->stock > 0 ? 'InStock' : 'OutOfStock' }}">

                                    @php
                                        $isAboveFold = $loop->index < 4;
                                    @endphp
                                    <a href="{{ route('producto.show', ['hash' => $producto->hash_id, 'slug' => $producto->slug]) }}"
                                        class="card-img-wrapper d-block" id="img-wrapper-{{ $producto->id }}">
                                        @if($producto->foto_producto)
                                            <x-product-img
                                                :path="$producto->foto_producto"
                                                class="card-img-top"
                                                :alt="$producto->detalle_producto . ' - ' . ($producto->marca->nombre_marca ?? '') . ' - El Porvenir Beauty Center'"
                                                itemprop="image"
                                                :width="400"
                                                :height="400"
                                                sizes="(max-width: 768px) 50vw, 25vw"
                                                decoding="async"
                                                :loading="$isAboveFold ? 'eager' : 'lazy'"
                                                :fetchpriority="$isAboveFold ? 'high' : 'auto'"
                                                onload="this.classList.add('img-loaded'); this.closest('.card-img-wrapper').classList.add('loaded')"
                                            />
                                        @else
                                            <div class="text-muted"><i class="fas fa-image fa-3x"></i></div>
                                        @endif
                                        @if($producto->oferta)
                                            <span class="position-absolute top-0 start-0 badge bg-danger text-white m-2"
                                                style="font-size: 0.8rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">🌟 OFERTA</span>
                                        @endif
                                        @if((int)$producto->stock === 0)
                                            <span class="position-absolute top-0 end-0 badge bg-danger text-white m-2"
                                                style="font-size: 0.8rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">No disponible</span>
                                        @elseif((int)$producto->stock < 5)
                                            <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">¡Pocas
                                                unidades!</span>
                                        @endif
                                    </a>
                                    <div class="card-body">
                                        <div>
                                            <span
                                                class="category-badge">{{ $producto->marca->nombre_marca ?? 'General' }}</span>
                                            <a href="{{ route('producto.show', ['hash' => $producto->hash_id, 'slug' => $producto->slug]) }}"
                                                class="product-title" itemprop="url">{{ $producto->detalle_producto }}</a>
                                            <div class="d-flex justify-content-center align-items-center mt-3">
                                                @if($producto->ultimaEntrada && $producto->ultimaEntrada->precio_venta)
                                                    <span class="product-price" itemprop="offers" itemscope
                                                        itemtype="https://schema.org/Offer">
                                                        <meta itemprop="price"
                                                            content="{{ $producto->ultimaEntrada->precio_venta }}">
                                                        <meta itemprop="priceCurrency" content="GTQ">
                                                        <link itemprop="availability" href="https://schema.org/{{ $producto->stock > 0 ? 'InStock' : 'OutOfStock' }}">
                                                        <meta itemprop="url" content="{{ route('producto.show', ['hash' => $producto->hash_id, 'slug' => $producto->slug]) }}">
                                                        Q. {{ number_format($producto->ultimaEntrada->precio_venta, 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">Precio no disponible</span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Botón Agregar al Carrito - Mobile Optimized con estado --}}
                                        @if($producto->ultimaEntrada && $producto->ultimaEntrada->precio_venta)
                                            @php
                                                $enCarrito = session('carrito') && isset(session('carrito')[$producto->id]);
                                                $sinStock  = (int)$producto->stock === 0;
                                            @endphp
                                            @if($sinStock)
                                                {{-- Sin stock: mostrar botón deshabilitado --}}
                                                <div class="mt-auto">
                                                    <button type="button" class="btn w-100 btn-secondary" disabled
                                                        style="font-size: 0.9rem; padding: 0.6rem 1rem; border-radius: 50px; opacity: 0.65; cursor: not-allowed;">
                                                        <i class="fas fa-ban me-1"></i> No disponible
                                                    </button>
                                                </div>
                                            @else
                                                <form action="{{ route('cart.agregar') }}" method="POST" class="mt-auto">
                                                    @csrf
                                                    <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                                    <input type="hidden" name="cantidad" value="1">
                                                    <button type="submit" class="btn w-100 {{ $enCarrito ? '' : 'btn-theme' }}"
                                                        style="font-size: 0.9rem; padding: 0.6rem 1rem; {{ $enCarrito ? 'background-color: var(--bs-primary); color: white; border: none; border-radius: 50px;' : '' }}">
                                                        @if($enCarrito)
                                                            <i class="fas fa-check me-1"></i> Agregado
                                                        @else
                                                            <i class="fas fa-cart-plus me-1"></i> Agregar
                                                        @endif
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-5">
                        {{ $productos->appends(['search' => $search, 'marca' => $marcaActual ? null : $marcaId, 'sort' => $sort ?? null])->links('vendor.pagination.shop-pagination') }}
                    </div>
                @else
                    <div class="alert alert-info text-center py-5">
                        <i class="fas fa-search fa-3x mb-3 text-info"></i>
                        <h4>No encontramos productos</h4>
                        <p>Intenta ajustar tus filtros o búsqueda.</p>
                        <a href="{{ route('tienda') }}" class="btn btn-outline-dark mt-2">Ver todo</a>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>El Porvenir Beauty Center</h5>
                    <p>Tu destino número uno para productos de belleza y cuidado personal. Calidad y servicio
                        garantizados.</p>
                </div>
                <div class="col-md-4 mb-4 text-center">
                    <h5>Enlaces Rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-decoration-none text-muted">Inicio</a></li>
                        <li><a href="{{ route('tienda.marcas') }}" class="text-decoration-none text-muted">Marcas</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Contacto</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4 text-md-end">
                    <h5>Síguenos</h5>
                    <div class="social-links">
                        <a href="https://www.facebook.com/profile.php?id=100068403559419" target="_blank"><i
                                class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/bcporvenir?igsh=Y2o3cjRreWpvN3B5" target="_blank"><i
                                class="fab fa-instagram"></i></a>
                    </div>
                    <p class="mt-3 small">&copy; {{ date('Y') }} Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @include('partials.cart-ajax')
</body>

</html>