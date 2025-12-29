<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beauty Center El Porvenir</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap"
        rel="stylesheet">

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
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link {{ Route::is('home') ? 'active' : '' }}"
                            href="{{ route('home') }}">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link {{ Route::is('contact') ? 'active' : '' }}"
                            href="{{ route('contact') }}">Contacto</a></li>
                    @auth
                        <li class="nav-item ms-lg-3">
                            <a href="{{ url('/dashboard') }}" class="btn btn-theme btn-sm">Mi Cuenta</a>
                        </li>
                    @else
                        <li class="nav-item ms-lg-3">
                            <a href="{{ route('login') }}" class="nav-link"><i class="fas fa-user"></i> Login</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container my-5">
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


                <!-- Sort/Filter Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <p class="mb-0 text-muted small text-nowrap">Resultados: {{ $productos->total() }}</p>

                    <div class="d-flex align-items-center gap-2">
                        <!-- Mobile Filter Toggle -->
                        <button class="btn btn-outline-dark btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                            <i class="fas fa-filter"></i> Filtros
                        </button>

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
                    <div class="row row-cols-1 row-cols-md-3 g-4">
                        @foreach($productos as $producto)
                            <div class="col">
                                <div class="card card-product">
                                    <div class="card-img-wrapper">
                                        @if($producto->foto_producto)
                                            <img src="{{ asset('storage/' . $producto->foto_producto) }}" class="card-img-top"
                                                alt="{{ $producto->detalle_producto }}">
                                        @else
                                            <div class="text-muted"><i class="fas fa-image fa-3x"></i></div>
                                        @endif
                                        @if($producto->stock <= 0)
                                            <span class="position-absolute top-0 end-0 badge bg-secondary m-2">Agotado</span>
                                        @elseif($producto->stock < 5)
                                            <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">¡Pocas
                                                unidades!</span>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <span class="category-badge">{{ $producto->marca->nombre_marca ?? 'General' }}</span>
                                        <a href="#" class="product-title">{{ $producto->detalle_producto }}</a>
                                        <div class="d-flex justify-content-center align-items-center mt-3">
                                            @if($producto->ultimaEntrada && $producto->ultimaEntrada->precio_venta)
                                                <span class="product-price">Q.
                                                    {{ number_format($producto->ultimaEntrada->precio_venta, 2) }}</span>
                                            @else
                                                <span class="text-muted small">Precio no disponible</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-5">
                        {{ $productos->appends(['search' => $search, 'marca' => $marcaId])->links('pagination::bootstrap-5') }}
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