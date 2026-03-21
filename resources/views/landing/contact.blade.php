<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Contacto Grupo Are">
    <title>Grupo Are | Contacto</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/about.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/blog.css">
    <link rel="stylesheet" href="css/contact.css">

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
                        <h3 class="sub-heading">Estamos para ayudarte</h3>
                        <h1 class="heading">Contacto</h1>
                        <p class="desc">
                            Escríbenos para resolver dudas, coordinar visitas o recibir más información sobre Grupo Are.
                        </p>
                        <a href="#contacto-formulario" class="default-btn">Enviar mensaje</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Contact One Start -->
    <div class="quick-contact-one">
        <div class="container">
            <div class="row">
                <div class="col-xl-4 col-lg-5">
                    <ul class="quick-contact-one__inner clearfix list-unstyled">
                        <li class="quick-contact-one__single">
                            <div class="quick-contact-one__single-icon">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div class="quick-contact-one__single-text">
                                <h3>¿Tienes preguntas?</h3>
                                <p>Déjanos tu número y te devolvemos la llamada.</p>
                            </div>
                            <div class="quick-contact-form-box">
                                <form id="quick-contact-form" class="contact-form-validated"
                                    action="assets/inc/sendemail.php" method="POST" novalidate="novalidate">
                                    <div class="form-group">
                                        <div class="input-box">
                                            <input type="text" name="form_phone" id="formPhonee"
                                                placeholder="Ingresa tu teléfono" value="">
                                        </div>
                                    </div>
                                    <button type="submit" data-loading-text="Please wait...">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                        <li class="quick-contact-one__single">
                            <div class="quick-contact-one__single-icon">
                                <i class="fa fa-solid fa-headset"></i>
                            </div>
                            <div class="quick-contact-one__single-text">
                                <h3>Atención por WhatsApp</h3>
                                <p>Contáctanos de forma rápida y directa.</p>
                            </div>
                            <div class="quick-contact-one__single-btn">
                                <a href="https://wa.me/573000000000" class="thm-btn" target="_blank" rel="noopener noreferrer">
                                    <span class="fas fa-arrow-right"></span>
                                    Iniciar chat
                                </a>
                            </div>
                        </li>
                        <li class="quick-contact-one__single quick-contact-one__single--innstyle2">
                            <div class="quick-contact-one__title">
                                <h4>Síguenos</h4>
                                <div class="quick-contact-one__social">
                                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                                    <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col-xl-8 col-lg-7">
                    <div class="quick-contact-one__location">
                        <div class="quick-contact-one__location-title">
                            <h3>Información de contacto</h3>
                        </div>
                        <div class="quick-contact-one__location-inner">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="quick-contact-one__location-single">
                                        <div class="quick-contact-one__location-icon">
                                            <i class="fa-solid fa-phone"></i>
                                        </div>
                                        <div class="quick-contact-one__location-text">
                                            <p>Teléfono</p>
                                            <h3><a href="tel:+573000000000">+57 300 000 0000</a></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="quick-contact-one__location-single">
                                        <div class="quick-contact-one__location-icon">
                                            <i class="fa-solid fa-envelope"></i>
                                        </div>
                                        <div class="quick-contact-one__location-text">
                                            <p>Correo</p>
                                            <h3><a href="mailto:contacto@grupoare.com">contacto@grupoare.com</a></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="quick-contact-one__location-single">
                                        <div class="quick-contact-one__location-icon">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </div>
                                        <div class="quick-contact-one__location-text">
                                            <p>Ubicación</p>
                                            <h3>Montería, Córdoba, Colombia</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="quick-contact-one__location-single">
                                        <div class="quick-contact-one__location-icon">
                                            <i class="fa-solid fa-clock"></i>
                                        </div>
                                        <div class="quick-contact-one__location-text">
                                            <p>Horario</p>
                                            <h3>Lun - Sáb: 8:30 a.m. a 5:30 p.m.</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="quick-contact-one__location-btn">
                                <a href="https://maps.google.com" class="thm-btn" target="_blank" rel="noopener noreferrer">
                                    <span class="fas fa-arrow-right"></span>
                                    Ver en mapa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Quick Contact One End -->


    <!-- Main Contact Form Start -->
    <section id="contacto-formulario" class="main-contact-form">
        <div class="container">
            <div class="main-contact-form__inner">
                <div class="section-title text-center sec-title-animation animation-style1">
                    <div class="section-title__tagline-box">
                        <span class="icon-tooth"><i class="fa-solid fa-comments"></i></span>
                        <p class="section-title__tagline">Formulario de contacto</p>
                    </div>
                    <h2 class="section-title__title title-animation">
                        Envíanos un <span>mensaje</span>
                    </h2>
                </div>
                <form id="contact-form" class="contact-form-validated contact-page__form"
                    action="assets/inc/sendemail.php" method="POST">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="contact-page__input">
                                <input type="text" name="name" placeholder="Tu nombre" required="">
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="contact-page__input">
                                <input type="email" name="email" placeholder="Tu correo electrónico" required="">
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="contact-page__input">
                                <input type="text" placeholder="Teléfono" name="phone" required="">
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="contact-page__input">
                                <input type="text" placeholder="Asunto" name="subject" required="">
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="contact-page__input">
                                <textarea name="message" placeholder="Mensaje" required=""></textarea>
                            </div>
                        </div>
                        <div class="contact-page__btn">
                            <button type="submit" class="thm-btn" data-loading-text="Please wait...">
                                <span class="fas fa-arrow-right"></span>
                                Enviar mensaje
                            </button>
                        </div>
                    </div>
                    <div class="result"></div>
                </form>
            </div>
        </div>
    </section>
    <!-- Main Contact Form End -->


    <!-- Google Map One Start -->
    <section class="google-map-one">
        <div class="container">
            <div class="google-map-one__inner">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4562.753041141002!2d-118.80123790098536!3d34.152323469614075!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80e82469c2162619%3A0xba03efb7998eef6d!2sCostco+Wholesale!5e0!3m2!1sbn!2sbd!4v1562518641290!5m2!1sbn!2sbd"
                    class="google-map__one-box" allowfullscreen></iframe>
            </div>
        </div>
    </section>
    <!-- Google Map One End -->

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

</body>

</html>

