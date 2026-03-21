<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Grupo Are">
    <title>Grupo Are</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/about.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('css/instalaciones.css') }}">
    <link rel="stylesheet" href="{{ asset('css/contact.css') }}">

    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">

</head>

<body>

    <header id="header" class="header-section">
        <div class="container">
            <nav class="navbar-custom">
                <a href="{{ route('landing.home') }}" class="navbar-brand">
                    <img src="{{ asset('images/logo.png') }}" alt="Grupo Are">
                </a>

                <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
                    ☰
                </button>

                <div id="navmenu" class="mainmenu">
                    <ul class="nav-list">
                        <li><a href="{{ route('landing.home') }}">Inicio</a></li>
                        <li><a href="{{ route('landing.about') }}">Nosotros</a></li>
                        <li><a href="{{ route('landing.personal') }}">Personal</a></li>
                        <li><a href="{{ route('landing.ventas') }}">Ventas</a></li>
                        <li><a href="{{ route('landing.recursos') }}">Eventos</a></li>
                        <li><a href="{{ route('landing.contact') }}">Contacto</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <section id="home" class="hero-section">
        <div class="container">
            <div class="hero-wrap row align-items-center">
                <div class="col-lg-7 col-md-9 col-12">
                    <div class="hero-content">
                        <h3 class="sub-heading">Tradición, calidad y excelencia</h3>
                        <h1 class="heading">Grupo Are</h1>
                        <p class="desc">
                            Impulsamos el desarrollo del sector bufalero con una visión moderna,
                            sólida y comprometida con la calidad, la producción y el crecimiento sostenible.
                        </p>
                        <a href="{{ route('landing.contact') }}" class="default-btn">Contáctenos</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--About Three Start -->
        <section id="about" class="about-three">
            <div class="container">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="about-three__left wow slideInLeft" data-animate="slide-left" data-wow-delay="100ms"
                            data-wow-duration="2500ms">
                            <div class="about-three__img-box">
                                <div class="about-three__img">
                                    <img src="{{ asset('images/about-three-img-1.png') }}" alt="">
                                </div>
                                <div class="about-three__img-two">
                                    <img src="{{ asset('images/about-three-img-2.png') }}" alt="">
                                </div>
                                <div class="about-three__img-three">
                                    <img src="{{ asset('images/about-three-img-3.png') }}" alt="">
                                </div>
                                <div class="about-three__experience-box">
                                    <div class="about-three__experience-count">
                                        <h3 class="odometer" data-count="25">15</h3>
                                        <span>+</span>
                                    </div>
                                    <p class="about-three__experience-count-text">Años de experiencia</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="about-three__right" data-animate="slide-right">
                            <div class="section-title text-left sec-title-animation animation-style2">
                                <div class="section-title__tagline-box">
                                    <span class="icon-pharmacy"><i class="fa-solid fa-house"></i></span>
                                    <p class="section-title__tagline">Sobre Grupo Are</p>
                                </div>
                                <h2 class="section-title__title title-animation split-text">Más de 25 años impulsando el
                                    <span>sector bufalero</span>
                                </h2>
                            </div>
                            <p class="about-three__text">En Grupo Are trabajamos con una visión moderna, sólida y
                                comprometida con la producción de calidad. Integramos experiencia, tecnología y buenas
                                prácticas para fortalecer toda la cadena del sector bufalero.</p>
                            <div class="about-three__content-box">
                                <div class="about-three__content-icon">
                                    <span class="icon-healthcare"></span>
                                </div>
                                <div class="about-three__content">
                                    <h4>Producción, comercialización y desarrollo</h4>
                                    <p>Ofrecemos soluciones integrales para el crecimiento sostenible del sector:
                                        acompañamiento técnico, visión empresarial y enfoque en resultados.</p>
                                </div>
                            </div>
                            <div class="about-three__points-box">
                                <ul class="about-three__points">
                                    <li>
                                        <div class="icon">
                                            <span class="fa-solid fa-check"></span>
                                        </div>
                                        <div class="text">
                                            <p>Calidad y trazabilidad.</p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="icon">
                                            <span class="fa-solid fa-check"></span>
                                        </div>
                                        <div class="text">
                                            <p>Experiencia comprobada.</p>
                                        </div>
                                    </li>
                                </ul>
                                <ul class="about-three__points about-three__points--two">
                                    <li>
                                        <div class="icon">
                                            <span class="fa-solid fa-check"></span>
                                        </div>
                                        <div class="text">
                                            <p>Innovación productiva.</p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="icon">
                                            <span class="fa-solid fa-check"></span>
                                        </div>
                                        <div class="text">
                                            <p>Crecimiento sostenible.</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="about-three__btn">
                                <a href="{{ route('landing.about') }}" class="thm-btn">
                                    <span class="fa-solid fa-arrow-right"></span>
                                    Conoce más
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--About Three End -->

        <section class="process-two">
            <div class="container">
                <div class="section-title text-center sec-title-animation animation-style1">
                    <div class="section-title__tagline-box">
                        <span class="icon-pharmacy"></span>
                        <p class="section-title__tagline">Nuestras Instalaciones</p>
                    </div>
                    <h2 class="section-title__title title-animation split-text">Infraestructura para una producción eficiente y sostenible</h2>
                </div>
                <ul class="row" data-stagger>
                    <!--Process Two Single Start -->
                    <li class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp animated" data-wow-delay="100ms" style="visibility: visible; animation-delay: 100ms; animation-name: fadeInUp;">
                        <div class="process-two__single">
                            <div class="process-two__img-box">
                                <div class="process-two__img">
                                    <img src="{{ asset('images/process-two-1-1.png') }}" alt="">
                                </div>
                                <div class="process-two__count"></div>
                            </div>
                            <div class="process-two__content">
                                <h3 class="process-two__title">Sala de Ordeño</h3>
                                <p class="process-two__text">Área diseñada para un ordeño higiénico, cómodo y eficiente,
                                    garantizando calidad en cada jornada.</p>
                            </div>
                        </div>
                    </li>
                    <!--Process Two Single End -->
                    <!--Process Two Single Start -->
                    <li class="col-xl-3 col-lg-6 col-md-6 wow fadeInDown animated" data-wow-delay="200ms" style="visibility: visible; animation-delay: 200ms; animation-name: fadeInDown;">
                        <div class="process-two__single">
                            <div class="process-two__img-box">
                                <div class="process-two__img">
                                    <img src="{{ asset('images/process-two-1-2.png') }}" alt="">
                                </div>
                                <div class="process-two__count"></div>
                            </div>
                            <div class="process-two__content">
                                <h3 class="process-two__title">Campos de Pastoreo</h3>
                                <p class="process-two__text">Espacios amplios y manejados con buenas prácticas para
                                    asegurar una alimentación natural y balanceada.</p>
                            </div>
                        </div>
                    </li>
                    <!--Process Two Single End -->
                    <!--Process Two Single Start -->
                    <li class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp animated" data-wow-delay="300ms" style="visibility: visible; animation-delay: 300ms; animation-name: fadeInUp;">
                        <div class="process-two__single">
                            <div class="process-two__img-box">
                                <div class="process-two__img">
                                    <img src="{{ asset('images/process-two-1-3.png') }}" alt="">
                                </div>
                                <div class="process-two__count"></div>
                            </div>
                            <div class="process-two__content">
                                <h3 class="process-two__title">Lagunas y Canales</h3>
                                <p class="process-two__text">Sistemas de agua que favorecen el bienestar animal y
                                    fortalecen el equilibrio ambiental de la finca.</p>
                            </div>
                        </div>
                    </li>
                    <!--Process Two Single End -->
                    <!--Process Two Single Start -->
                    <li class="col-xl-3 col-lg-6 col-md-6 wow fadeInDown animated" data-wow-delay="400ms" style="visibility: visible; animation-delay: 400ms; animation-name: fadeInDown;">
                        <div class="process-two__single">
                            <div class="process-two__img-box">
                                <div class="process-two__img">
                                    <img src="{{ asset('images/process-two-1-4.png') }}" alt="">
                                </div>
                                <div class="process-two__count"></div>
                            </div>
                            <div class="process-two__content">
                                <h3 class="process-two__title">Establos Modernos</h3>
                                <p class="process-two__text">Instalaciones funcionales que optimizan la operación diaria,
                                    priorizando confort, limpieza y seguridad.</p>
                            </div>
                        </div>
                    </li>
                    <!--Process Two Single End -->
                </ul>
            </div>
        </section>

        <section id="portfolio" class="ventas-section">
            <div class="container">
                <div class="section-title text-center sec-title-animation animation-style1">
                    <div class="section-title__tagline-box">
                        <span class="icon-pharmacy"><i class="fa-solid fa-tag"></i></span>
                        <p class="section-title__tagline">Ventas</p>
                    </div>
                    <h2 class="section-title__title title-animation split-text">Búfalas disponibles para venta</h2>
                </div>

                <div class="row g-4" data-stagger>
                    @forelse($sales as $sale)
                        @php
                            $photo = $sale->photo_path ? asset('storage/' . $sale->photo_path) : asset('images/mediterranea.jpg');
                            $waPhone = preg_replace('/\D+/', '', (string) $sale->phone);
                            $message = rawurlencode('Hola, me interesa el animal ' . $sale->code . ' (Código: ' . $sale->code . ')');
                            $statusText = match($sale->status) {
                                'reserved' => 'Reservada',
                                'sold' => 'Vendida',
                                default => 'Disponible',
                            };
                            $sexText = $sale->sex === 'male' ? 'Macho' : 'Hembra';
                        @endphp
                        <div class="col-xl-4 col-md-6">
                            <article class="venta-card">
                                <div class="venta-card__image">
                                    <img src="{{ $photo }}" alt="{{ $sale->code }}">
                                    <span class="venta-card__badge venta-card__badge--{{ $sale->status }}">{{ $statusText }}</span>
                                </div>
                                <div class="venta-card__body">
                                    <div class="venta-card__code">{{ $sale->code }}</div>
                                    <ul class="venta-card__details">
                                        <li><span>Sexo:</span> {{ $sexText }}</li>
                                        @if($sale->breed)<li><span>Raza:</span> {{ $sale->breed->name }}</li>@endif
                                        @if($sale->age_years)<li><span>Edad:</span> {{ $sale->age_years }} años</li>@endif
                                        @if($sale->fatherBreed)<li><span>Raza Padre:</span> {{ $sale->fatherBreed->name }}</li>@endif
                                        @if($sale->motherBreed)<li><span>Raza Madre:</span> {{ $sale->motherBreed->name }}</li>@endif
                                        <li><span>Precio:</span> ₡{{ number_format($sale->price_crc, 0, ',', '.') }}</li>
                                    </ul>
                                    <div class="venta-card__actions">
                                        <a href="https://wa.me/{{ $waPhone }}?text={{ $message }}" class="venta-card__wa" target="_blank" rel="noopener noreferrer">
                                            <i class="fa-brands fa-whatsapp"></i>
                                            <span>Contactar</span>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="desc text-center" style="max-width: 720px; margin: 0 auto;">
                                Aún no hay publicaciones de venta disponibles.
                            </p>
                        </div>
                    @endforelse
                </div>

                @if($sales->count())
                <div class="text-center" style="margin-top: 40px;">
                    <a href="{{ route('landing.ventas') }}" class="thm-btn">
                        <span class="fa-solid fa-arrow-right"></span>
                        Ver todas las disponibles
                    </a>
                </div>
                @endif
            </div>
        </section>

        <section class="appointment-two">
            <div class="appointment-two__shape-1">
                <img src="assets/images/shapes/appointment-two-shape-1.png" alt="">
            </div>
            <div class="appointment-two__shape-2">
                <img src="assets/images/shapes/appointment-two-shape-2.png" alt="">
            </div>
            <div class="appointment-two__shape-3"></div>
            <div class="appointment-two__shape-4"></div>
            <div class="appointment-two__shape-5"></div>
            <div class="appointment-two__shape-6"></div>
            <div class="container">
                <div class="appointment-two__inner">
                    <div class="row">
                        <div class="col-xl-6"></div>
                        <div class="col-xl-6">
                            <div class="appointment-two__form-box">
                                <h3 class="appointment-two__title">Contáctanos</h3>
                                <form class="contact-form-validated appointment-two__form" action="assets/inc/sendemail.php" method="post" novalidate="novalidate">
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-6">
                                            <h4 class="appointment-two__input-title">Nombre completo *</h4>
                                            <div class="appointment-two__input-box">
                                                <input type="text" name="name" placeholder="Tu nombre" required="" aria-required="true">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6">
                                            <h4 class="appointment-two__input-title">Correo electrónico *</h4>
                                            <div class="appointment-two__input-box">
                                                <input type="email" name="email" placeholder="tucorreo@dominio.com" required="" aria-required="true">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6">
                                            <h4 class="appointment-two__input-title">Asunto *</h4>
                                            <div class="appointment-two__input-box">
                                                <input type="text" name="subject" placeholder="Escribe el asunto" required="" aria-required="true">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6">
                                            <h4 class="appointment-two__input-title">Teléfono de contacto *</h4>
                                            <div class="appointment-two__input-box">
                                                <input type="text" name="phone" placeholder="Tu teléfono" required="" aria-required="true">
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <h4 class="appointment-two__input-title">Mensaje *</h4>
                                            <div class="appointment-two__input-box text-message-box">
                                                <textarea name="message" placeholder="Escribe tu mensaje"></textarea>
                                            </div>
                                            <div class="appointment-two__btn-box">
                                                <button type="submit" class="thm-btn">
                                                    <span class="fas fa-arrow-right"></span>
                                                    Enviar mensaje
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="result"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="appointment-two__img wow slideInLeft animated" data-wow-delay="100ms" data-wow-duration="2500ms" style="visibility: visible; animation-duration: 2500ms; animation-delay: 100ms; animation-name: slideInLeft;">
                        <img src="{{ asset('images/contac.jpg') }}" alt="">
                    </div>
                </div>
            </div>
        </section>

        <footer class="site-footer">
            <div class="container">
                <div class="row g-4 align-items-start">
                    <div class="col-lg-4 col-md-6">
                        <div class="footer-brand">
                            <img src="{{ asset('images/logo.png') }}" alt="Grupo Are" class="footer-logo">
                            <p>
                                Más de 25 años impulsando el sector bufalero con visión moderna,
                                calidad productiva y compromiso sostenible.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="footer-links">
                            <h4>Enlaces rápidos</h4>
                            <ul>
                                <li><a href="{{ route('landing.home') }}">Inicio</a></li>
                                <li><a href="{{ route('landing.about') }}">Nosotros</a></li>
                                <li><a href="{{ route('landing.personal') }}">Personal</a></li>
                                <li><a href="{{ route('landing.ventas') }}">Ventas</a></li>
                                <li><a href="{{ route('landing.recursos') }}">Eventos</a></li>
                                <li><a href="{{ route('landing.contact') }}">Contacto</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="footer-contact">
                            <h4>Contacto</h4>
                            <p>Grupo Are</p>
                            <p>Email: contacto@grupoare.com</p>
                            <p>Tel: +57 300 000 0000</p>
                            <div class="footer-social">
                                <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                                <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                                <a href="#" aria-label="Whatsapp"><i class="fa-brands fa-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; 2026 Grupo Are. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const navmenu = document.getElementById('navmenu');

        menuToggle.addEventListener('click', () => {
            navmenu.classList.toggle('active');
        });
    </script>
    <script src="{{ asset('js/animations.js') }}"></script>

</body>

</html>

