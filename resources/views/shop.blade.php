<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap CSS (puedes reemplazar con la versión que estés utilizando) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Font Awesome (puedes reemplazar con la versión que estés utilizando) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <!-- Estilos adicionales (ajusta según tus necesidades) -->
    <style>
        body {
            padding-top: 56px;
            /* Ajusta según la altura de la barra de navegación */
            overflow-x: hidden;

        }

        .navbar {
            background-color: #FFB6C1;
            /* Color de fondo de la barra de navegación */
        }

        .navbar-brand {
            color: #ffffff;
            /* Color del texto de la marca en la barra de navegación */
        }

        .navbar-brand:hover {
            color: #ffffff;
            /* Color del texto de la marca en la barra de navegación al pasar el ratón */
        }

        .form-inline .form-control {
            width: 300px;
            /* Ancho del campo de búsqueda */
        }

        /* Estilos adicionales para los íconos de redes sociales */
        .social-icons i {
            font-size: 24px;
            margin-right: 10px;
            color: #ffffff;
            /* Color de los íconos de redes sociales */
        }
    </style>
</head>

<body>
    <!-- Barra de navegación horizontal -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">

        <a class="navbar-brand" href="#">BCPorvenir</a>

        <!-- Botón de alternancia para dispositivos móviles -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Contenido de la barra de navegación -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Formulario de búsqueda con icono de lupa -->
            <form class="form-inline ml-auto" method="GET" action="{{ route('shop') }}">
                <div class="input-group">
                    <input class="form-control" type="search" placeholder="Buscar producto" aria-label="Search"
                        name="search">
                    <div class="input-group-append">
                        <button class="btn btn-light" type="submit">
                            <i class="fas fa-search"></i> <!-- Icono de lupa -->
                        </button>
                    </div>
                </div>
            </form>


            <!-- Íconos de redes sociales -->
            <div class="social-icons ml-2">
                <a href="https://www.facebook.com/profile.php?id=100068403559419" target="_blank"
                    class="social-icon-link">
                    <i class="fab fa-facebook"></i>
                </a>

                <a href="https://www.instagram.com/bcporvenir?igsh=Y2o3cjRreWpvN3B5" target="_blank"
                    class="social-icon-link">
                    <i class="fab fa-instagram"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mt-5">
        @yield('content')
    </div>

    <!-- Bootstrap y JavaScript (puedes reemplazar con las versiones que estés utilizando) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

@section('js')

    <script>
        const zoomImages = document.querySelectorAll('.zoom');

        zoomImages.forEach(image => {
            image.addEventListener('mouseover', () => {
                image.style.transform = 'scale(1.5)';
            });

            image.addEventListener('mouseout', () => {
                image.style.transform = 'scale(1)';
            });
        });
    </script>
@endsection

@section('content')
<div class="container" style="margin-top: 50px">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/shop">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tienda</li>
        </ol>
    </nav>

    @if($productos->count() > 0)
        <div class="row">
            @foreach($productos as $producto)
                <div class="col-lg-3 col-md-4 col-6 mb-3">
                    <div class="card border-0" style="margin-bottom: 20px; height: auto;">
                        <img src="{{ asset('storage/' . $producto->foto_producto) }}"
                            class="card-img-top mx-auto img-fluid zoom"
                            style="height: 180px; width: 100%; object-fit: contain; background: white ; padding: 5px;"
                            alt="{{ $producto->nombre }}">

                        <div class="card-body">
                            <h5 class="card-title">Precio: Q. {{ $producto->ultimaEntrada?->precio_venta ? number_format($producto->ultimaEntrada->precio_venta, 2) : '-' }}</h5>
                            <p class="card-text">{{ $producto->marca->nombre_marca }}</p>
                            <p class="card-text">{{ $producto->detalle_producto }}</p>
                            {{-- Disponibilidad --}}
                            @if($producto->stock > 0)
                                <span class="badge badge-success">Disponible</span>
                            @else
                                <span class="badge badge-danger">No disponible</span>
                            @endif

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p>No se encontraron resultados para la búsqueda.</p>
    @endif
</div>
<style>
    .zoom {
        transition: transform 0.2s;
    }

    .zoom:hover {
        transform: scale(1.5);
    }
</style>