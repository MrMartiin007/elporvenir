<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- SEO Meta Tags --}}
    <x-seo-meta title="Contacto - Beauty Center El Porvenir | Puerto Barrios, Izabal"
        description="Contáctanos para consultas sobre nuestros productos de belleza y cuidado personal. Estamos ubicados en Puerto Barrios, Izabal, Guatemala. WhatsApp, teléfono y redes sociales disponibles."
        keywords="contacto, Beauty Center El Porvenir, Puerto Barrios, Izabal, Guatemala, teléfono, whatsapp, ubicación, tienda de belleza"
        :image="asset('logo.jpg')" url="https://elporvenir.shop/contacto" type="website" />

    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
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

        .btn-theme {
            background-color: #333;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            transition: all 0.3s;
            border: none;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .btn-theme:hover {
            background-color: var(--bs-primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(216, 164, 164, 0.4);
        }

        .contact-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid #f8f8f8;
            padding: 3rem;
        }

        .form-control {
            padding: 0.8rem 1rem;
            border-radius: 8px;
            border: 1px solid #eee;
            background-color: #fcfcfc;
        }

        .form-control:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 0.25rem rgba(216, 164, 164, 0.25);
            background-color: white;
        }

        .contact-info-item {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background-color: var(--bs-primary-light);
            color: var(--bs-primary-dark);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-right: 1.5rem;
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
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link active" href="{{ route('contact') }}">Contacto</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h1 class="text-center mb-5 display-5">Contáctanos</h1>

                <div class="row g-5">
                    <!-- Contact Form -->
                    <div class="col-md-7">
                        <div class="contact-card h-100">
                            <h4 class="mb-4">Envíanos un mensaje</h4>
                            <form>
                                <div class="mb-3">
                                    <label for="name" class="form-label text-muted small text-uppercase fw-bold">Nombre
                                        Completo</label>
                                    <input type="text" class="form-control" id="name" placeholder="Tu nombre">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label text-muted small text-uppercase fw-bold">Correo
                                        Electrónico</label>
                                    <input type="email" class="form-control" id="email"
                                        placeholder="nombre@ejemplo.com">
                                </div>
                                <div class="mb-3">
                                    <label for="phone"
                                        class="form-label text-muted small text-uppercase fw-bold">Teléfono</label>
                                    <input type="tel" class="form-control" id="phone" placeholder="+502 ...">
                                </div>
                                <div class="mb-4">
                                    <label for="message"
                                        class="form-label text-muted small text-uppercase fw-bold">Mensaje</label>
                                    <textarea class="form-control" id="message" rows="4"
                                        placeholder="¿En qué podemos ayudarte?"></textarea>
                                </div>
                                <button type="submit" class="btn btn-theme w-100">Enviar Mensaje</button>
                            </form>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="col-md-5">
                        <div class="ps-md-4 mt-4 mt-md-0">
                            <div class="contact-info-item">
                                <div class="contact-icon">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">Llámanos</h6>
                                    <p class="mb-0 text-muted">+502 3899 5635</p>
                                </div>
                            </div>

                            <div class="contact-info-item">
                                <div class="contact-icon">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">WhatsApp</h6>
                                    <p class="mb-0 text-muted"><a href="https://wa.me/50238995635"
                                            class="text-decoration-none text-muted">Chatear ahora</a></p>
                                </div>
                            </div>

                            <div class="contact-info-item">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">Email</h6>
                                    <p class="mb-0 text-muted">info@beautycenter.com</p>
                                </div>
                            </div>

                            <div class="contact-info-item">
                                <div class="contact-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">Ubicación</h6>
                                    <p class="mb-0 text-muted">El Porvenir, Guatemala</p>
                                </div>
                            </div>

                            <div class="mt-5 p-4 rounded" style="background-color: var(--bs-primary-light);">
                                <h5 class="mb-3" style="color: var(--bs-primary-dark);">Horario de Atención</h5>
                                <ul class="list-unstyled text-muted mb-0">
                                    <li class="d-flex justify-content-between mb-2"><span>Lunes - Viernes:</span>
                                        <strong>9:00 AM - 6:00 PM</strong>
                                    </li>
                                    <li class="d-flex justify-content-between mb-2"><span>Sábado:</span> <strong>9:00 AM
                                            - 1:00 PM</strong></li>
                                    <li class="d-flex justify-content-between"><span>Domingo:</span>
                                        <strong>Cerrado</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
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
                        <li><a href="{{ route('home') }}" class="text-decoration-none text-muted">Inicio</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Marcas</a></li>
                        <li><a href="{{ route('contact') }}" class="text-decoration-none text-muted">Contacto</a></li>
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