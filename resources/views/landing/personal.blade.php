<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Personal de Grupo Are">
    <title>Grupo Are | Personal</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/about.css') }}">
    <link rel="stylesheet" href="{{ asset('css/personal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">

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

                <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">☰</button>

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
                <div class="col-lg-8 col-md-10 col-12">
                    <div class="hero-content">
                        <h3 class="sub-heading">Nuestro equipo</h3>
                        <h1 class="heading">Personal Grupo Are</h1>
                        <p class="desc">
                            Conoce al personal que impulsa día a día la operación y el crecimiento del grupo.
                        </p>
                        <a href="#equipo" class="default-btn">Ver equipo</a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section id="equipo" class="team-page">
        <div class="container">
            <div class="section-title text-center sec-title-animation animation-style1">
                <div class="section-title__tagline-box">
                    <span class="icon-pharmacy"><i class="fa-solid fa-users"></i></span>
                    <p class="section-title__tagline">Nuestro equipo</p>
                </div>
                <h2 class="section-title__title title-animation split-text">Personal de Grupo Are</h2>
            </div>

            <div class="row g-4 justify-content-center" data-stagger>
                @forelse($staff as $member)
                    @php
                        $photo = $member->photo_path
                            ? asset('storage/' . $member->photo_path)
                            : ($member->photo_url ?: asset('images/team-page-v1-1.jpg'));
                    @endphp
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="team-two__single">
                            <div class="team-two__img-box">
                                <div class="team-two__img">
                                    <img src="{{ $photo }}" alt="{{ $member->photo_alt ?? $member->name }}">
                                </div>
                            </div>
                            <div class="team-two__content">
                                <p class="team-two__designation">{{ $member->role }}</p>
                                <h3 class="team-two__name">{{ $member->name }}</h3>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="desc text-center" style="max-width: 720px; margin: 0 auto;">
                            Información del equipo próximamente.
                        </p>
                    </div>
                @endforelse
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

