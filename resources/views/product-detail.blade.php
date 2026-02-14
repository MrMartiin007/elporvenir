<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- SEO Meta Tags - Dinámicos para cada producto --}}
    <x-seo-meta
        title="{{ $producto->detalle_producto }} - {{ $producto->marca->nombre_marca ?? 'Beauty Center El Porvenir' }} | Beauty Center El Porvenir"
        description="Compra {{ $producto->detalle_producto }} de {{ $producto->marca->nombre_marca ?? 'las mejores marcas' }} en Beauty Center El Porvenir, Puerto Barrios. {{ $producto->ultimaEntrada ? 'Precio: Q. ' . number_format($producto->ultimaEntrada->precio_venta, 2) : '' }} ¡Envío a toda Guatemala!"
        keywords="{{ $producto->detalle_producto }}, {{ $producto->marca->nombre_marca ?? '' }}, cosméticos, belleza, Puerto Barrios, Guatemala"
        :image="$producto->foto_producto ? asset('storage/' . $producto->foto_producto) : asset('logo.jpg')"
        url="{{ route('producto.show', $producto->id) }}" type="product" />

    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap"
        rel="stylesheet">

    {{-- Schema.org Structured Data - Product --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "Product",
        "name": "{{ $producto->detalle_producto }}",
        "image": "{{ $producto->foto_producto ? asset('storage/' . $producto->foto_producto) : asset('logo.jpg') }}",
        "description": "{{ $producto->detalle_producto }} de {{ $producto->marca->nombre_marca ?? 'Beauty Center El Porvenir' }}",
        "brand": {
            "@type": "Brand",
            "name": "{{ $producto->marca->nombre_marca ?? 'Beauty Center El Porvenir' }}"
        },
        "sku": "{{ $producto->codigo_producto }}",
        @if($producto->ultimaEntrada && $producto->ultimaEntrada->precio_venta)
            "offers": {
                "@type": "Offer",
                "url": "{{ route('producto.show', $producto->id) }}",
                "priceCurrency": "GTQ",
                "price": "{{ $producto->ultimaEntrada->precio_venta }}",
                "availability": "https://schema.org/{{ $producto->stock > 0 ? 'InStock' : 'OutOfStock' }}",
                "seller": {
                    "@type": "Organization",
                    "name": "Beauty Center El Porvenir"
                }
            },
        @endif
        "url": "{{ route('producto.show', $producto->id) }}"
    }
    </script>

    {{-- Schema.org BreadcrumbList --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Inicio",
                "item": "{{ route('home') }}"
            },
            @if($producto->marca)
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "{{ $producto->marca->nombre_marca }}",
                    "item": "{{ route('home', ['marca' => $producto->marcas_id]) }}"
                },
                {
                    "@type": "ListItem",
                    "position": 3,
                    "name": "{{ $producto->detalle_producto }}"
                }
            @else
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "{{ $producto->detalle_producto }}"
                }
            @endif
        ]
    }
    </script>

    <style>
        :root {
            --bs-primary: #d8a4a4;
            --bs-primary-dark: #b0657b;
            --bs-primary-light: #f9ebeb;
        }

        body {
            font-family: 'Lato', sans-serif;
            background-color: #fcf8f8;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .navbar-brand {
            font-family: 'Playfair Display', serif;
        }

        /* Navbar */
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 0.8rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #212529;
        }

        .navbar-brand span {
            color: var(--bs-primary);
        }

        .nav-link {
            font-weight: 500;
            color: #495057;
            margin-left: 1rem;
            transition: color 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--bs-primary-dark);
        }

        /* Search bar */
        .search-bar {
            position: relative;
            max-width: 320px;
        }

        .search-bar input {
            border: 1px solid #e0e0e0;
            border-radius: 50px;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            font-size: 0.9rem;
            transition: border-color 0.3s, box-shadow 0.3s;
            background: #f9f9f9;
        }

        .search-bar input:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(216, 164, 164, 0.15);
            background: #fff;
            outline: none;
        }

        .search-bar .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 0.85rem;
            pointer-events: none;
        }

        .search-bar button {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: var(--bs-primary-dark);
            color: white;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            transition: background 0.3s;
        }

        .search-bar button:hover {
            background: #333;
        }

        /* Mobile search */
        .mobile-search-bar {
            background: white;
            padding: 0.6rem 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        .mobile-search-bar .search-bar {
            max-width: 100%;
        }

        /* Breadcrumb */
        .breadcrumb-custom {
            background: transparent;
            padding: 0;
            margin-bottom: 0;
        }

        .breadcrumb-custom .breadcrumb-item a {
            color: var(--bs-primary-dark);
            text-decoration: none;
            transition: color 0.2s;
        }

        .breadcrumb-custom .breadcrumb-item a:hover {
            color: #333;
        }

        .breadcrumb-custom .breadcrumb-item.active {
            color: #999;
        }

        /* Product Layout - Desktop: two columns */
        .product-layout {
            display: flex;
            gap: 3rem;
            align-items: flex-start;
        }

        /* Product Image Section */
        .product-img-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
            min-height: 450px;
            overflow: hidden;
            position: sticky;
            top: 120px;
        }

        .product-img-section img {
            max-height: 450px;
            max-width: 100%;
            object-fit: contain;
            transition: transform 0.5s ease;
        }

        .product-img-section:hover img {
            transform: scale(1.05);
        }

        /* Product Info Panel */
        .product-info-content {
            flex: 1;
            padding: 0.5rem 0;
        }

        .product-brand {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--bs-primary-dark);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .product-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #222;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .product-code {
            font-size: 0.85rem;
            color: #999;
            margin-bottom: 1.5rem;
        }

        .product-price {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--bs-primary-dark);
            margin-bottom: 1.5rem;
        }

        .stock-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .stock-badge.in-stock {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .stock-badge.low-stock {
            background: #fff3e0;
            color: #e65100;
        }

        .stock-badge.out-stock {
            background: #fce4ec;
            color: #c62828;
        }

        /* Desktop action buttons */
        .desktop-actions .btn-add-cart {
            background-color: #333;
            color: white;
            padding: 0.9rem 2rem;
            border-radius: 50px;
            transition: all 0.3s;
            border: none;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.5px;
        }

        .desktop-actions .btn-add-cart:hover {
            background-color: var(--bs-primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(216, 164, 164, 0.4);
        }

        .desktop-actions .btn-add-cart.added {
            background-color: var(--bs-primary);
            color: white;
        }

        .desktop-actions .btn-whatsapp-product {
            background-color: #25d366;
            color: white;
            padding: 0.9rem 2rem;
            border-radius: 50px;
            transition: all 0.3s;
            border: none;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
        }

        .desktop-actions .btn-whatsapp-product:hover {
            background-color: #128c7e;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.3);
        }

        /* Mobile sticky bottom bar */
        .mobile-action-bar {
            display: none;
        }

        /* Related Products */
        .related-section {
            margin-top: 4rem;
        }

        .related-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: #333;
            position: relative;
            padding-bottom: 0.8rem;
        }

        .related-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--bs-primary);
            border-radius: 3px;
        }

        .card-product {
            border: none;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #f0f0f0;
            text-decoration: none;
        }

        .card-product:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(216, 164, 164, 0.15);
            border-color: var(--bs-primary-light);
        }

        .card-img-wrapper {
            position: relative;
            height: 200px;
            overflow: hidden;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-img-top {
            max-height: 100%;
            width: auto;
            max-width: 100%;
            transition: transform 0.5s;
        }

        .card-product:hover .card-img-top {
            transform: scale(1.05);
        }

        .card-body {
            padding: 1.2rem;
            text-align: center;
        }

        .card-category {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999;
            margin-bottom: 0.3rem;
        }

        .card-title-product {
            font-size: 0.95rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .card-price {
            color: var(--bs-primary-dark);
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* Footer */
        footer {
            background-color: #1a1a1a;
            color: #bbb;
            padding: 4rem 0 2rem;
            margin-top: 5rem;
        }

        footer h5 {
            color: white;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        .social-links a {
            color: white;
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.05);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 0.6rem;
            text-decoration: none;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background: var(--bs-primary);
            color: white;
            transform: translateY(-3px);
        }

        /* WhatsApp Float */
        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .whatsapp-float:hover {
            background-color: #128C7E;
            transform: scale(1.1);
            color: white;
        }

        /* Divider */
        .divider {
            border-top: 1px solid #eee;
            margin: 1.5rem 0;
        }

        /* ========== MOBILE ========== */
        @media (max-width: 991px) {
            .navbar-brand {
                font-size: 1.1rem;
            }

            .navbar-brand img {
                max-height: 50px !important;
                width: 50px !important;
            }
        }

        @media (max-width: 767px) {
            body {
                padding-bottom: 80px;
                /* space for sticky bar */
            }

            /* Single card on mobile */
            .product-layout {
                flex-direction: column;
                gap: 0;
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
                overflow: hidden;
            }

            .product-img-section {
                flex: none;
                width: 100%;
                min-height: auto;
                padding: 1rem;
                border-radius: 0;
                box-shadow: none;
                background: #fafafa;
                position: static;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .product-img-section img {
                max-height: 280px;
            }

            .product-info-content {
                padding: 1rem 1.2rem 1.2rem;
            }

            .product-brand {
                font-size: 0.75rem;
                letter-spacing: 1.5px;
                margin-bottom: 0.3rem;
            }

            .product-title {
                font-size: 1.25rem;
                margin-bottom: 0.5rem;
            }

            .product-code {
                font-size: 0.8rem;
                margin-bottom: 1rem;
            }

            .product-price {
                font-size: 1.6rem;
                margin-bottom: 0.8rem;
            }

            .stock-badge {
                font-size: 0.8rem;
                padding: 0.3rem 0.8rem;
                margin-bottom: 1rem;
            }

            .divider {
                margin: 1rem 0;
            }

            /* Hide desktop action buttons on mobile */
            .desktop-actions {
                display: none;
            }

            /* Show sticky bottom action bar */
            .mobile-action-bar {
                display: flex;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: white;
                padding: 0.7rem 1rem;
                box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
                z-index: 1000;
                gap: 0.6rem;
                align-items: center;
            }

            .mobile-action-bar .mobile-price {
                font-weight: 700;
                color: var(--bs-primary-dark);
                font-size: 1.1rem;
                white-space: nowrap;
                min-width: fit-content;
            }

            .mobile-action-bar .btn-add-cart-mobile {
                flex: 1;
                background-color: #333;
                color: white;
                padding: 0.7rem 1rem;
                border-radius: 50px;
                border: none;
                font-weight: 600;
                font-size: 0.85rem;
            }

            .mobile-action-bar .btn-add-cart-mobile.added {
                background-color: var(--bs-primary);
            }

            .mobile-action-bar .btn-wa-mobile {
                background-color: #25d366;
                color: white;
                border: none;
                border-radius: 50%;
                width: 44px;
                height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                flex-shrink: 0;
            }

            /* WhatsApp float hidden on mobile — we have it in the action bar */
            .whatsapp-float {
                display: none;
            }

            /* Related */
            .related-section {
                margin-top: 2.5rem;
            }

            .related-title {
                font-size: 1.2rem;
                margin-bottom: 1.2rem;
            }

            .card-img-wrapper {
                height: 140px;
            }

            .card-body {
                padding: 0.8rem;
            }

            .card-title-product {
                font-size: 0.82rem;
                margin-bottom: 0.3rem;
            }

            .card-price {
                font-size: 0.95rem;
            }

            .card-category {
                font-size: 0.65rem;
            }

            /* Footer compact */
            footer {
                margin-top: 3rem;
                padding: 2.5rem 0 1.5rem;
            }

            footer .row>div {
                margin-bottom: 1.5rem !important;
            }

            /* Breadcrumb */
            .breadcrumb-custom {
                font-size: 0.82rem;
            }

            /* Extra info compact */
            .extra-info {
                font-size: 0.8rem;
            }

            .extra-info i {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 400px) {
            .product-img-section img {
                max-height: 200px;
            }

            .product-title {
                font-size: 1.1rem;
            }

            .product-price {
                font-size: 1.4rem;
            }

            .mobile-action-bar .mobile-price {
                font-size: 0.95rem;
            }

            .mobile-action-bar .btn-add-cart-mobile {
                font-size: 0.78rem;
                padding: 0.6rem 0.8rem;
            }
        }
    </style>
</head>

<body>

    <!-- WhatsApp Button -->
    <a href="https://wa.me/50238995635" class="whatsapp-float" target="_blank" title="Contáctanos por WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="{{ asset('logo.jpg') }}" alt="Beauty Center El Porvenir - Logo" width="80" height="auto"
                    class="d-inline-block align-text-top me-2" style="max-height: 80px; object-fit: contain;">
                Beauty Center <span>El Porvenir</span>
            </a>

            {{-- Carrito visible siempre en móvil --}}
            <a class="nav-link position-relative d-lg-none ms-auto me-2" href="{{ route('shop.index') }}"
                title="Ver Carrito">
                <i class="fas fa-shopping-cart" style="font-size: 1.5rem;"></i>
                @if(($carritoCount ?? 0) > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
                        style="background-color: var(--bs-primary); color: white; font-size: 0.65rem;">
                        {{ $carritoCount }}
                    </span>
                @endif
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contacto</a></li>
                    {{-- Desktop search bar --}}
                    <li class="nav-item d-none d-lg-block ms-3">
                        <form action="{{ route('home') }}" method="GET" class="search-bar">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" class="form-control" placeholder="Buscar productos...">
                            <button type="submit"><i class="fas fa-arrow-right"></i></button>
                        </form>
                    </li>
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link position-relative" href="{{ route('shop.index') }}" title="Ver Carrito">
                            <i class="fas fa-shopping-cart" style="font-size: 1.2rem;"></i>
                            @if(($carritoCount ?? 0) > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
                                    style="background-color: var(--bs-primary); color: white; font-size: 0.7rem;">
                                    {{ $carritoCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- Mobile search bar (below navbar) --}}
    <div class="mobile-search-bar d-lg-none">
        <div class="container">
            <form action="{{ route('home') }}" method="GET" class="search-bar">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" class="form-control" placeholder="Buscar productos...">
                <button type="submit"><i class="fas fa-arrow-right"></i></button>
            </form>
        </div>
    </div>

    <!-- Content -->
    <div class="container mt-4 mb-5">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-home"></i> Inicio</a></li>
                @if($producto->marca)
                    <li class="breadcrumb-item">
                        <a
                            href="{{ route('home', ['marca' => $producto->marcas_id]) }}">{{ $producto->marca->nombre_marca }}</a>
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
                    <img src="{{ asset('storage/' . $producto->foto_producto) }}"
                        alt="{{ $producto->detalle_producto }} - {{ $producto->marca->nombre_marca ?? '' }} - Beauty Center El Porvenir"
                        loading="eager">
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
                    <a href="{{ route('home', ['marca' => $producto->marcas_id]) }}"
                        class="product-brand text-decoration-none d-block">
                        {{ $producto->marca->nombre_marca }}
                    </a>
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

                <!-- Stock Status -->
                @if($producto->stock > 5)
                    <div class="stock-badge in-stock">
                        <i class="fas fa-check-circle me-2"></i> Disponible
                    </div>
                @elseif($producto->stock > 0)
                    <div class="stock-badge low-stock">
                        <i class="fas fa-exclamation-circle me-2"></i> ¡Pocas unidades!
                    </div>
                @else
                    <div class="stock-badge low-stock">
                        <i class="fas fa-exclamation-circle me-2"></i> Pocas unidades
                    </div>
                @endif

                <div class="divider"></div>

                <!-- Actions (desktop only) -->
                <div class="d-flex flex-column gap-3 desktop-actions">
                    @if($producto->ultimaEntrada && $producto->ultimaEntrada->precio_venta)
                        <form action="{{ route('shop.agregar') }}" method="POST">
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
                            <a href="{{ route('producto.show', $rel->id) }}" class="card card-product text-decoration-none">
                                <div class="card-img-wrapper">
                                    @if($rel->foto_producto)
                                        <img src="{{ asset('storage/' . $rel->foto_producto) }}" class="card-img-top"
                                            alt="{{ $rel->detalle_producto }}" loading="lazy">
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
                    <h5>Beauty Center El Porvenir</h5>
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

    <!-- Mobile Sticky Action Bar -->
    <div class="mobile-action-bar">
        @if($producto->ultimaEntrada && $producto->ultimaEntrada->precio_venta)
            <span class="mobile-price">Q. {{ number_format($producto->ultimaEntrada->precio_venta, 2) }}</span>
            <form action="{{ route('shop.agregar') }}" method="POST" style="flex: 1;">
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
        <a href="https://wa.me/50238995635?text={{ urlencode('Hola, me interesa el producto: ' . $producto->detalle_producto . ' (Código: ' . $producto->codigo_producto . '). ¿Está disponible?') }}"
            target="_blank" class="btn-wa-mobile">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>