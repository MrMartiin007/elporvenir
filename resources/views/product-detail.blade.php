<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- SEO Meta Tags - Dinámicos para cada producto --}}
    <x-seo-meta
        title="{{ $producto->detalle_producto }} - {{ $producto->marca->nombre_marca ?? 'El Porvenir' }} | El Porvenir Puerto Barrios"
        description="Compra {{ $producto->detalle_producto }} de {{ $producto->marca->nombre_marca ?? 'las mejores marcas' }} en El Porvenir, Puerto Barrios, Guatemala. {{ $producto->ultimaEntrada ? 'Precio: Q. ' . number_format($producto->ultimaEntrada->precio_venta, 2) : '' }} ¡Envío a toda Guatemala!"
        keywords="{{ $producto->detalle_producto }}, {{ $producto->marca->nombre_marca ?? '' }}, cosméticos, belleza, Puerto Barrios, Guatemala"
        :image="$producto->foto_producto ? asset('storage/' . $producto->foto_producto) : asset('logo.jpg')"
        url="{{ route('producto.show', ['hash' => $producto->hash_id, 'slug' => $producto->slug]) }}" type="product" />

    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap"
        rel="stylesheet">

    {{-- Schema.org: Product (oferta, envío, GTIN) y migas de pan. Se arma en PHP para escapar bien comillas y acentos. --}}
    @php
        $ldFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP;
        $nombreMarca = $producto->marca->nombre_marca ?? null;
        $envioProducto = \Illuminate\Support\Facades\Cache::remember(
            'tarifa_envio_activa',
            600,
            fn () => (float) (\App\Models\TarifaEnvio::where('activo', true)->latest()->value('costo') ?? 35.00)
        );

        $ldProducto = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $producto->detalle_producto,
            'image' => [$producto->imagen_url ?? asset('logo.jpg')],
            'description' => $producto->detalle_producto
                . ($nombreMarca ? ' de ' . $nombreMarca : '')
                . '. Disponible en El Porvenir Beauty Center, Puerto Barrios, con envío a toda Guatemala.',
            'sku' => $producto->codigo_producto,
            'brand' => ['@type' => 'Brand', 'name' => $nombreMarca ?? 'El Porvenir Beauty Center'],
            'url' => $producto->url,
        ];
        if ($producto->gtin) {
            $ldProducto['gtin'] = $producto->gtin;
        }
        if ($producto->precio_actual) {
            $ldProducto['offers'] = [
                '@type' => 'Offer',
                'url' => $producto->url,
                'priceCurrency' => 'GTQ',
                'price' => $producto->precio_actual,
                'priceValidUntil' => now()->addDays(30)->toDateString(),
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability' => 'https://schema.org/' . ($producto->en_stock ? 'InStock' : 'OutOfStock'),
                'seller' => ['@type' => 'Organization', 'name' => 'El Porvenir Beauty Center'],
                'shippingDetails' => [
                    '@type' => 'OfferShippingDetails',
                    'shippingRate' => ['@type' => 'MonetaryAmount', 'value' => number_format($envioProducto, 2, '.', ''), 'currency' => 'GTQ'],
                    'shippingDestination' => ['@type' => 'DefinedRegion', 'addressCountry' => 'GT'],
                ],
            ];
        }

        $ldMigasProducto = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_values(array_filter([
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => route('home')],
                $producto->marca ? ['@type' => 'ListItem', 'position' => 2, 'name' => $nombreMarca, 'item' => $producto->marca->url()] : null,
                ['@type' => 'ListItem', 'position' => $producto->marca ? 3 : 2, 'name' => $producto->detalle_producto],
            ])),
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($ldProducto, $ldFlags) !!}</script>
    <script type="application/ld+json">{!! json_encode($ldMigasProducto, $ldFlags) !!}</script>

    @vite(['resources/css/shop-product.css', 'resources/css/shop-buscador.css', 'resources/css/shop-tema.css'])
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
    <div class="container mt-4 mb-5">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-home"></i> Inicio</a></li>
                @if($producto->marca)
                    <li class="breadcrumb-item">
                        <a
                            href="{{ $producto->marca->url() }}">{{ $producto->marca->nombre_marca }}</a>
                    </li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $producto->detalle_producto }}</li>
            </ol>
        </nav>

        <!-- Product Detail -->
        <div class="product-layout">
            {{-- Image --}}
            <div class="product-img-section">
                @if($producto->foto_producto)
                    <x-product-img :path="$producto->foto_producto"
                        :alt="$producto->detalle_producto . ' - ' . ($producto->marca->nombre_marca ?? '') . ' - El Porvenir Beauty Center'"
                        sizes="(max-width: 768px) 90vw, 500px" loading="eager" fetchpriority="high" />
                @else
                    <div class="text-muted text-center">
                        <i class="fas fa-image fa-5x mb-3"></i>
                        <p>Imagen no disponible</p>
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="product-info-content">
                @if($producto->marca)
                    <a href="{{ $producto->marca->url() }}"
                        class="product-brand text-decoration-none d-block">
                        {{ $producto->marca->nombre_marca }}
                    </a>
                @endif

                @if($producto->oferta)
                    <span class="badge bg-danger text-white mb-2 py-1 px-3 mt-1"
                        style="font-size: 0.85rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 50px;">🌟 OFERTA
                        DESTACADA</span>
                @endif

                <h1 class="product-title">{{ $producto->detalle_producto }}</h1>

                @if($producto->ultimaEntrada && $producto->ultimaEntrada->precio_venta)
                    <div class="product-price">
                        Q. {{ number_format($producto->ultimaEntrada->precio_venta, 2) }}
                    </div>
                @else
                    <div class="product-price text-muted" style="font-size: 1.2rem;">
                        Precio no disponible
                    </div>
                @endif

                {{-- Stock Status --}}
                @php $stockActual = (int)$producto->stock; @endphp
                @if($stockActual > 5)
                    <div class="stock-badge in-stock">
                        <i class="fas fa-check-circle me-2"></i> Disponible
                    </div>
                @elseif($stockActual > 0)
                    <div class="stock-badge low-stock">
                        <i class="fas fa-exclamation-circle me-2"></i> ¡Pocas unidades!
                    </div>
                @else
                    <div class="stock-badge out-of-stock"
                        style="background:#fdecea; color:#c0392b; border:1px solid #f5c6cb;">
                        <i class="fas fa-times-circle me-2"></i> No disponible
                    </div>
                @endif

                <div class="divider"></div>

                {{-- Actions (desktop only) --}}
                <div class="d-flex flex-column gap-3 desktop-actions">
                    @if($producto->ultimaEntrada && $producto->ultimaEntrada->precio_venta)
                        @if($stockActual === 0)
                            {{-- Sin stock: botón deshabilitado --}}
                            <button type="button" class="btn btn-add-cart w-100" disabled
                                style="opacity:0.6; cursor:not-allowed; background:#6c757d; color:white; border:none;">
                                <i class="fas fa-ban me-2"></i> No disponible
                            </button>
                        @else
                            <form action="{{ route('cart.agregar') }}" method="POST">
                                @csrf
                                <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                <input type="hidden" name="cantidad" value="1">
                                <button type="submit" class="btn btn-add-cart w-100 {{ $enCarrito ? 'added' : '' }}">
                                    @if($enCarrito)
                                        <i class="fas fa-check me-2"></i> Agregado al carrito
                                    @else
                                        <i class="fas fa-cart-plus me-2"></i> Agregar al carrito
                                    @endif
                                </button>
                            </form>
                        @endif
                    @endif

                    <a href="https://wa.me/50238995635?text={{ urlencode('Hola, me interesa el producto: ' . $producto->detalle_producto . ' (Código: ' . $producto->codigo_producto . '). ¿Está disponible?') }}"
                        target="_blank" class="btn btn-whatsapp-product w-100">
                        <i class="fab fa-whatsapp me-2"></i> Consultar por WhatsApp
                    </a>
                </div>

                <div class="divider"></div>

                <!-- Extra Info -->
                <div class="text-muted small extra-info">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-shipping-fast me-2" style="color: var(--bs-primary-dark);"></i>
                        Envío a toda Guatemala
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-shield-alt me-2" style="color: var(--bs-primary-dark);"></i>
                        Productos 100% originales
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-store me-2" style="color: var(--bs-primary-dark);"></i>
                        Disponible en tienda física — Puerto Barrios
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relacionados->count() > 0)
            <div class="related-section">
                <h2 class="related-title">Productos Relacionados</h2>
                <div class="row row-cols-2 row-cols-md-4 g-3">
                    @foreach($relacionados as $rel)
                        <div class="col">
                            <a href="{{ route('producto.show', ['hash' => $rel->hash_id, 'slug' => $rel->slug]) }}"
                                class="card card-product text-decoration-none">
                                <div class="card-img-wrapper">
                                    @if($rel->foto_producto)
                                        <x-product-img :path="$rel->foto_producto" class="card-img-top"
                                            :alt="$rel->detalle_producto" sizes="(max-width: 768px) 50vw, 25vw"
                                            loading="lazy" decoding="async" />
                                    @else
                                        <div class="text-muted"><i class="fas fa-image fa-3x"></i></div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <span class="card-category">{{ $rel->marca->nombre_marca ?? 'General' }}</span>
                                    <h3 class="card-title-product">{{ $rel->detalle_producto }}</h3>
                                    @if($rel->ultimaEntrada && $rel->ultimaEntrada->precio_venta)
                                        <span class="card-price">Q.
                                            {{ number_format($rel->ultimaEntrada->precio_venta, 2) }}</span>
                                    @endif
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

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
                        <li><a href="{{ route('home') }}" class="text-decoration-none text-muted">Inicio</a></li>
                        <li><a href="{{ route('contact') }}" class="text-decoration-none text-muted">Contacto</a>
                        </li>
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

    {{-- Mobile Sticky Action Bar --}}
    <div class="mobile-action-bar">
        @if($producto->ultimaEntrada && $producto->ultimaEntrada->precio_venta)
            <span class="mobile-price">Q. {{ number_format($producto->ultimaEntrada->precio_venta, 2) }}</span>
            @if($stockActual === 0)
                {{-- Sin stock: botón deshabilitado --}}
                <button type="button" class="btn-add-cart-mobile w-100" disabled
                    style="flex:1; opacity:0.6; cursor:not-allowed; background:#6c757d; color:white; border:none; border-radius:50px; padding:0.7rem 1rem; font-weight:600; font-size:0.85rem;">
                    <i class="fas fa-ban me-1"></i> No disponible
                </button>
            @else
                <form action="{{ route('cart.agregar') }}" method="POST" style="flex: 1;">
                    @csrf
                    <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                    <input type="hidden" name="cantidad" value="1">
                    <button type="submit" class="btn-add-cart-mobile w-100 {{ $enCarrito ? 'added' : '' }}">
                        @if($enCarrito)
                            <i class="fas fa-check me-1"></i> Agregado
                        @else
                            <i class="fas fa-cart-plus me-1"></i> Agregar
                        @endif
                    </button>
                </form>
            @endif
        @endif
        <a href="https://wa.me/50238995635?text={{ urlencode('Hola, me interesa el producto: ' . $producto->detalle_producto . ' (Código: ' . $producto->codigo_producto . '). ¿Está disponible?') }}"
            target="_blank" class="btn-wa-mobile">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @include('partials.cart-ajax')
</body>

</html>