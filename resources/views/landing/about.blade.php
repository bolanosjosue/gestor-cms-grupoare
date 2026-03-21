<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Historia de Grupo Are">
    <title>Grupo Are | Nosotros</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/about.css">
    <link rel="stylesheet" href="css/about-timeline.css">
    <link rel="stylesheet" href="css/responsive.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">
</head>

<body>
    <header id="header" class="header-section">
        <div class="container">
            <nav class="navbar-custom">
                <a href="{{ route('landing.home') }}" class="navbar-brand">
                    <img src="images/logo.png" alt="Grupo Are">
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
                        <h3 class="sub-heading">Nuestra historia</h3>
                        <h1 class="heading">Grupo Are</h1>
                        <p class="desc">
                            Un recorrido de crecimiento, compromiso y evolución en el sector bufalero.
                        </p>
                        <a href="#about" class="default-btn">Ver trayectoria</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="about-three">
        <div class="container">
            <div class="row">
                <div class="col-xl-6">
                    <div class="about-three__left wow slideInLeft" data-wow-delay="100ms" data-wow-duration="2500ms">
                        <div class="about-three__img-box">
                            <div class="about-three__img">
                                <img src="images/about-three-img-1.png" alt="">
                            </div>
                            <div class="about-three__img-two">
                                <img src="images/about-three-img-2.png" alt="">
                            </div>
                            <div class="about-three__img-three">
                                <img src="images/about-three-img-3.png" alt="">
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
                    <div class="about-three__right">
                        <div class="section-title text-left sec-title-animation animation-style2">
                            <div class="section-title__tagline-box">
                                <span class="icon-pharmacy"><i class="fa-solid fa-house"></i></span>
                                <p class="section-title__tagline">Sobre Grupo Are</p>
                            </div>
                            <h2 class="section-title__title title-animation">Más de 25 años impulsando el
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

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--About Three End -->

    <section class="about-timeline">
        <div class="container">
            <div class="section-title text-center sec-title-animation animation-style1">
                <div class="section-title__tagline-box">
                    <span class="icon-pharmacy"><i class="fa-solid fa-clock-rotate-left"></i></span>
                    <p class="section-title__tagline">Nuestra Historia</p>
                </div>
                <h2 class="section-title__title title-animation">Evolución de Grupo Are en el sector bufalero</h2>
            </div>

            <div class="about-timeline__track">
                <article class="about-timeline__item about-timeline__item--left">
                    <span class="about-timeline__year">2001</span>
                    <div class="about-timeline__card">
                        <div class="about-timeline__media">
                            <img src="images/history-1.png" alt="Inicio de Grupo Are en 2001">
                        </div>
                        <h3>Inicio del proyecto</h3>
                        <p>Comenzamos con una visión clara: desarrollar una operación bufalera sólida, técnica y
                            sostenible.</p>
                    </div>
                </article>

                <article class="about-timeline__item about-timeline__item--right">
                    <span class="about-timeline__year">2008</span>
                    <div class="about-timeline__card">
                        <div class="about-timeline__media">
                            <img src="images/history-2.png" alt="Mejoras productivas en 2008">
                        </div>
                        <h3>Mejoras productivas</h3>
                        <p>Implementamos prácticas de manejo y control que elevaron la calidad de nuestros procesos.</p>
                    </div>
                </article>

                <article class="about-timeline__item about-timeline__item--left">
                    <span class="about-timeline__year">2015</span>
                    <div class="about-timeline__card">
                        <div class="about-timeline__media">
                            <img src="images/history-3.png" alt="Infraestructura moderna en 2015">
                        </div>
                        <h3>Infraestructura moderna</h3>
                        <p>Fortalecimos establos, pastoreo y sistemas hídricos para optimizar bienestar y rendimiento.
                        </p>
                    </div>
                </article>

                <article class="about-timeline__item about-timeline__item--right">
                    <span class="about-timeline__year">2020</span>
                    <div class="about-timeline__card">
                        <div class="about-timeline__media">
                            <img src="images/history-4.png" alt="Consolidación comercial en 2020">
                        </div>
                        <h3>Consolidación comercial</h3>
                        <p>Expandimos nuestras líneas de comercialización, manteniendo trazabilidad y estándares de
                            calidad.</p>
                    </div>
                </article>

                <article class="about-timeline__item about-timeline__item--left">
                    <span class="about-timeline__year">Hoy</span>
                    <div class="about-timeline__card">
                        <div class="about-timeline__media">
                            <video autoplay muted loop playsinline>
                                <source src="images/history-5.mp4" type="video/mp4">
                                Tu navegador no soporta video.
                            </video>
                        </div>
                        <h3>Proyección de crecimiento</h3>
                        <p>Seguimos innovando para impulsar el sector bufalero con enfoque empresarial y compromiso
                            sostenible.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <footer class="site-footer">
        <div class="container">
            <div class="row g-4 align-items-start">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand">
                        <img src="images/logo.png" alt="Grupo Are" class="footer-logo">
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
</body>

</html>
