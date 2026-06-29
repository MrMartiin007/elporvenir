<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- SEO Meta Tags --}}
    <x-seo-meta title="El Porvenir Beauty Center - Carrito de Compras"
        description="Finaliza tu compra de productos de belleza y cuidado personal en Puerto Barrios, Izabal."
        keywords="carrito, compras, cosméticos, belleza, Puerto Barrios" :image="asset('logo.jpg')"
        url="https://elporvenir.com.gt/" type="website" />

    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap"
        rel="stylesheet">

    <style>
        /* Estilos base - mismos que shop.blade.php */
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

        .btn-theme {
            background-color: #333;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            transition: all 0.3s;
            border: none;
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
        }

        /* Mobile-First Optimizations */
        @media (max-width: 767px) {
            .navbar-brand {
                font-size: 1.1rem;
            }

            .navbar-brand img {
                width: 60px !important;
            }

            h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>

<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="{{ asset('logo.jpg') }}" alt="Logo" width="80" height="auto"
                    class="d-inline-block align-text-top me-2" style="max-height: 80px; object-fit: contain;">
                El Porvenir <span>Beauty Center</span>
            </a>

            {{-- Carrito visible siempre en móvil --}}
            <a class="nav-link position-relative d-lg-none me-2" href="{{ route('cart.index') }}" title="Ver Carrito">
                <i class="fas fa-shopping-cart" style="font-size: 1.3rem;"></i>
                @if(session('carrito') && count(session('carrito')) > 0)
                    @php $count = collect(session('carrito'))->sum('cantidad'); @endphp
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
                        style="background-color: var(--bs-primary); color: white; font-size: 0.7rem;">
                        {{ $count }}
                    </span>
                @endif
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contacto</a></li>

                    {{-- Carrito (solo desktop, en móvil está fuera del menú) --}}
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link position-relative" href="{{ route('cart.index') }}" title="Ver Carrito">
                            <i class="fas fa-shopping-cart" style="font-size: 1.2rem;"></i>
                            @if(session('carrito') && count(session('carrito')) > 0)
                                @php $count = collect(session('carrito'))->sum('cantidad'); @endphp
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
                                    style="background-color: var(--bs-primary); color: white; font-size: 0.7rem;">
                                    {{ $count }}
                                </span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    @yield('content')

    {{-- Footer --}}
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>El Porvenir Beauty Center</h5>
                    <p>Tu destino número uno para productos de belleza y cuidado personal.</p>
                </div>
                <div class="col-md-4 mb-4 text-center">
                    <h5>Enlaces Rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-decoration-none text-muted">Inicio</a></li>
                        <li><a href="{{ route('contact') }}" class="text-decoration-none text-muted">Contacto</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4 text-md-end">
                    <h5>Síguenos</h5>
                    <p class="mt-3 small">&copy; {{ date('Y') }} Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>