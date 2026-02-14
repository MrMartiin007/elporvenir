<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- SEO Meta Tags --}}
    <x-seo-meta title="Beauty Center El Porvenir - Cosméticos y Cuidado Personal en Puerto Barrios"
        description="Descubre los mejores productos de belleza y cuidado personal en Puerto Barrios, Izabal. Amplio catálogo de cosméticos, maquillaje, cuidado de la piel, perfumes y más. ¡Calidad garantizada!"
        keywords="cosméticos, belleza, cuidado personal, maquillaje, skincare, perfumes, Puerto Barrios, Izabal, Guatemala, productos de belleza, beauty center"
        :image="asset('logo.jpg')" url="https://elporvenir.shop/" type="website" />

    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap"
        rel="stylesheet">

    {{-- Schema.org Structured Data - Local Business --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BeautySalon",
        "name": "Beauty Center El Porvenir",
        "alternateName": ["El Porvenir", "Tienda El Porvenir", "BC El Porvenir"],
        "description": "El Porvenir es un beauty center y tienda de cosméticos en Puerto Barrios, Izabal, Guatemala. Productos de belleza, maquillaje y cuidado personal de las mejores marcas con envío a toda Guatemala.",
        "image": "{{ asset('logo.jpg') }}",
        "@id": "https://elporvenir.shop/",
        "url": "https://elporvenir.shop/",
        "telephone": "+502-3899-5635",
        "priceRange": "$$",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Puerto Barrios",
            "addressLocality": "Puerto Barrios",
            "addressRegion": "Izabal",
            "postalCode": "",
            "addressCountry": "GT"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": 15.7308,
            "longitude": -88.5992
        },
        "openingHoursSpecification": {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": [
                "Monday",
                "Tuesday",
                "Wednesday",
                "Thursday",
                "Friday",
                "Saturday"
            ],
            "opens": "08:00",
            "closes": "18:00"
        },
        "sameAs": [
            "https://www.facebook.com/profile.php?id=100068403559419",
            "https://www.instagram.com/bcporvenir?igsh=Y2o3cjRreWpvN3B5"
        ]
    }
    </script>

    <style>
        /* Colors Variables */
        :root {
            --bs-primary: #d8a4a4;
            /* Nude Pink Primary */
            --bs-primary-dark: #b0657b;
            /* Darker Pink for Hover/Text */
            --bs-primary-light: #f9ebeb;
            /* Very Light Pink for Backgrounds */
        }

        body {
            font-family: 'Lato', sans-serif;
            background-color: #fcf8f8;
            /* Slight pinkish white */
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
            padding: 1rem 0;
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

        /* Product Card */
        .card-product {
            border: none;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            background: white;
            border-radius: 12px;
            /* More rounded */
            overflow: hidden;
            border: 1px solid #f0f0f0;
        }

        .card-product:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(216, 164, 164, 0.15);
            /* Soft pink shadow */
            border-color: var(--bs-primary-light);
        }

        .card-img-wrapper {
            position: relative;
            height: 250px;
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
            padding: 1.5rem;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .category-badge {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999;
            margin-bottom: 0.5rem;
            display: block;
        }

        .product-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #333;
            text-decoration: none;
            display: block;
            transition: color 0.2s;
        }

        .product-title:hover {
            color: var(--bs-primary-dark);
        }

        .product-price {
            color: var(--bs-primary-dark);
            font-weight: 700;
            font-size: 1.25rem;
        }

        /* Sidebar Widgets */
        .sidebar-widget {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            margin-bottom: 2rem;
            border: 1px solid #f8f8f8;
        }

        .widget-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1.2rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.8rem;
            position: relative;
            color: var(--bs-primary-dark);
        }

        .widget-title::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 40px;
            height: 3px;
            background-color: var(--bs-primary);
            border-radius: 3px;
        }

        /* Improved Sidebar List */
        .list-group-flush .list-group-item {
            border: none;
            padding: 0.6rem 0.8rem;
            margin-bottom: 5px;
            font-size: 0.95rem;
            color: #555;
            background: transparent;
            border-radius: 8px;
            /* Professional rounded look */
            transition: all 0.2s;
        }

        .list-group-item a {
            color: inherit;
            text-decoration: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .list-group-item:hover {
            background-color: var(--bs-primary-light);
            color: var(--bs-primary-dark);
        }

        .list-group-item.active {
            background-color: var(--bs-primary-light);
            color: var(--bs-primary-dark);
            font-weight: 600;
        }

        .list-group-item.active a {
            /* Reset default active color */
            color: var(--bs-primary-dark);
        }

        .badge-count {
            background-color: #f1f1f1;
            color: #777;
            font-size: 0.75rem;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
        }

        .list-group-item:hover .badge-count,
        .list-group-item.active .badge-count {
            background-color: white;
            color: var(--bs-primary-dark);
        }

        /* Pagination */
        .pagination .page-link {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            line-height: 24px;
            text-align: center;
            margin: 0 4px;
            color: #444;
            border: 1px solid #eee;
        }

        .pagination .page-link:hover {
            background-color: var(--bs-primary-light);
            color: var(--bs-primary-dark);
            border-color: var(--bs-primary-light);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
            color: white;
            box-shadow: 0 4px 10px rgba(216, 164, 164, 0.4);
        }

        .btn-theme {
            background-color: #333;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            transition: all 0.3s;
        }

        .btn-theme:hover {
            background-color: var(--bs-primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(216, 164, 164, 0.4);
        }

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

        /* Whatsapp Button */
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

        .horizontal-scroll {
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 5px;
            /* Space for shadow if needed */
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            /* Firefox */
        }

        .horizontal-scroll::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari, Opera */
        }

        .brand-pill {
            display: inline-block;
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            background: white;
            color: #555;
            border: 1px solid #eee;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.9rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
        }

        .brand-pill:hover {
            background-color: var(--bs-primary-light);
            color: var(--bs-primary-dark);
        }

        .brand-pill.active {
            background-color: var(--bs-primary);
            color: white;
            border-color: var(--bs-primary);
            box-shadow: 0 4px 10px rgba(216, 164, 164, 0.4);
        }

        /* Mobile Product Grid Optimizations */
        @media (max-width: 767px) {
            .card-product {
                border-radius: 10px;
            }

            .card-img-wrapper {
                height: 200px;
            }

            .card-body {
                padding: 1rem;
            }

            .product-title {
                font-size: 0.9rem;
                line-height: 1.3;
            }

            .category-badge {
                font-size: 0.65rem;
            }

            .product-price {
                font-size: 1.1rem;
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
                <img src="{{ asset('logo.jpg') }}" alt="Logo" width="80" height="auto"
                    class="d-inline-block align-text-top me-2" style="max-height: 80px; object-fit: contain;">
                Beauty Center <span>El Porvenir</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Carrito visible siempre en móvil (a la derecha) --}}
            <a class="nav-link position-relative d-lg-none ms-auto" href="{{ route('shop.index') }}"
                title="Ver Carrito">
                <i class="fas fa-shopping-cart" style="font-size: 1.9rem;"></i>
                @if(($carritoCount ?? 0) > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
                        style="background-color: var(--bs-primary); color: white; font-size: 0.7rem;">
                        {{ $carritoCount }}
                        <span class="visually-hidden">productos en carrito</span>
                    </span>
                @endif
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link {{ Route::is('home') ? 'active' : '' }}"
                            href="{{ route('home') }}">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link {{ Route::is('contact') ? 'active' : '' }}"
                            href="{{ route('contact') }}">Contacto</a></li>

                    {{-- Carrito de Compras (solo desktop, en móvil está fuera del menú) --}}
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link position-relative" href="{{ route('shop.index') }}" title="Ver Carrito">
                            <i class="fas fa-shopping-cart" style="font-size: 1.2rem;"></i>
                            @if(($carritoCount ?? 0) > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
                                    style="background-color: var(--bs-primary); color: white; font-size: 0.7rem;">
                                    {{ $carritoCount }}
                                    <span class="visually-hidden">productos en carrito</span>
                                </span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container mt-3 mt-lg-5 mb-5">
        <div class="row">

            <!-- Sidebar (Desktop: Static | Mobile: Offcanvas) -->
            <div class="col-lg-3 order-2 order-lg-1 d-none d-lg-block">
                <!-- Desktop Sidebar Content (duplicated for simplicity or use partials in real app) -->
                @include('partials.sidebar_content')
            </div>

            <!-- Mobile Offcanvas Sidebar -->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas"
                aria-labelledby="sidebarOffcanvasLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="sidebarOffcanvasLabel">Filtros</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    @include('partials.sidebar_content')
                </div>
            </div>

            <!-- Product Grid -->
            <div class="col-lg-9 order-1 order-lg-2">

                <!-- Mobile: Sticky Search & Brand Nav -->
                <div class="d-block d-lg-none mb-4">
                    <!-- Search Bar -->
                    <form action="{{ route('home') }}" method="GET" class="mb-3">
                        {{-- Keep sort if present, but clear marca when searching --}}
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        <div class="input-group shadow-sm" style="border-radius: 50px; overflow: hidden;">
                            <span class="input-group-text bg-white border-0 ps-3"><i
                                    class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-0 py-2"
                                placeholder="¿Qué estás buscando?" value="{{ $search ?? '' }}"
                                style="box-shadow: none;">
                        </div>
                    </form>

                    <!-- Horizontal Brand Pills -->
                    <!-- Horizontal Brand Pills -->
                    <div class="horizontal-scroll d-flex gap-2 pb-2 align-items-center ps-1">
                        <!-- 'Todas' Pill -->
                        <a href="{{ route('home', request()->only('sort')) }}"
                            class="brand-pill {{ !($marcaId ?? false) ? 'active' : '' }} d-flex align-items-center justify-content-center border shadow-sm"
                            style="width: 50px; height: 50px; min-width: 50px; padding: 0; border-radius: 50%;">
                            <span class="small fw-bold">Todas</span>
                        </a>
                        @foreach($marcas as $marca)
                            {{-- Clear search when selecting brand, keep only sort --}}
                            <a href="{{ route('home', array_merge(request()->only('sort'), ['marca' => $marca->id])) }}"
                                class="brand-pill {{ ($marcaId ?? null) == $marca->id ? 'active' : '' }} p-0 d-flex align-items-center justify-content-center border shadow-sm"
                                title="{{ $marca->nombre_marca }}"
                                style="width: 50px; height: 50px; min-width: 50px; border-radius: 50%; overflow: hidden;">
                                @if($marca->foto_marca)
                                    <img src="{{ asset('storage/' . $marca->foto_marca) }}" alt="{{ $marca->nombre_marca }}"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <span class="small fw-bold text-uppercase">{{ substr($marca->nombre_marca, 0, 2) }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>


                <!-- Sort/Filter Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <p class="mb-0 text-muted small text-nowrap">Resultados: {{ $productos->total() }}</p>

                    <div class="d-flex align-items-center gap-2">
                        <!-- Mobile Filter Toggle (Hidden as we moved filters to main view) -->
                        <!-- <button class="btn btn-outline-dark btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                            <i class="fas fa-filter"></i> Filtros
                        </button> -->

                        <form id="sortForm" action="{{ route('home') }}" method="GET"
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
                                    <link itemprop="availability"
                                        href="http://schema.org/{{ $producto->stock > 0 ? 'InStock' : 'OutOfStock' }}">

                                    <a href="{{ route('producto.show', $producto->id) }}" class="card-img-wrapper d-block">
                                        @if($producto->foto_producto)
                                            <img src="{{ asset('storage/' . $producto->foto_producto) }}" class="card-img-top"
                                                alt="{{ $producto->detalle_producto }} - {{ $producto->marca->nombre_marca ?? '' }} - Beauty Center El Porvenir"
                                                itemprop="image" loading="lazy">
                                        @else
                                            <div class="text-muted"><i class="fas fa-image fa-3x"></i></div>
                                        @endif
                                        @if($producto->stock < 5)
                                            <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">¡Pocas
                                                unidades!</span>
                                        @endif
                                    </a>
                                    <div class="card-body">
                                        <div>
                                            <span
                                                class="category-badge">{{ $producto->marca->nombre_marca ?? 'General' }}</span>
                                            <a href="{{ route('producto.show', $producto->id) }}" class="product-title"
                                                itemprop="url">{{ $producto->detalle_producto }}</a>
                                            <div class="d-flex justify-content-center align-items-center mt-3">
                                                @if($producto->ultimaEntrada && $producto->ultimaEntrada->precio_venta)
                                                    <span class="product-price" itemprop="offers" itemscope
                                                        itemtype="https://schema.org/Offer">
                                                        <meta itemprop="price"
                                                            content="{{ $producto->ultimaEntrada->precio_venta }}">
                                                        <meta itemprop="priceCurrency" content="GTQ">
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
                                            @endphp
                                            <form action="{{ route('shop.agregar') }}" method="POST" class="mt-auto">
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
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-5">
                        {{ $productos->appends(['search' => $search, 'marca' => $marcaId, 'sort' => $sort ?? null])->links('vendor.pagination.shop-pagination') }}
                    </div>
                @else
                    <div class="alert alert-info text-center py-5">
                        <i class="fas fa-search fa-3x mb-3 text-info"></i>
                        <h4>No encontramos productos</h4>
                        <p>Intenta ajustar tus filtros o búsqueda.</p>
                        <a href="{{ route('home') }}" class="btn btn-outline-dark mt-2">Ver todo</a>
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
                    <h5>Beauty Center El Porvenir</h5>
                    <p>Tu destino número uno para productos de belleza y cuidado personal. Calidad y servicio
                        garantizados.</p>
                </div>
                <div class="col-md-4 mb-4 text-center">
                    <h5>Enlaces Rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-decoration-none text-muted">Inicio</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Marcas</a></li>
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
</body>

</html>