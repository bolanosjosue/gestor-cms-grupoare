<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Eventos Grupo Are">
    <title>Grupo Are | Eventos</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/about.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('css/blog.css') }}">
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
                        <h3 class="sub-heading">Publicaciones</h3>
                        <h1 class="heading">Eventos</h1>
                        <p class="desc">
                            Artículos, noticias y eventos del sector bufalero publicados por Grupo Are.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our blog Section start -->
    <div class="page-blog">
        <div class="container">
            <div class="row" data-stagger>
                @forelse($articles as $article)
                <div class="col-lg-4 col-md-6">
                    <div class="blog-item wow fadeInUp" data-wow-delay="{{ 0.25 * ($loop->iteration) }}s">
                        <div class="post-featured-image">
                            <figure class="image-anime">
                                <a href="{{ route('landing.blog.show', $article->slug) }}">
                                    <img src="{{ $article->getFinalCoverImageUrl() ?? 'images/blog-1-1.jpg' }}" alt="{{ $article->cover_image_alt ?? $article->title }}">
                                </a>
                            </figure>
                        </div>

                        <div class="post-item-body">
                            <h2><a href="{{ route('landing.blog.show', $article->slug) }}">{{ $article->title }}</a></h2>
                            <p>{{ $article->excerpt }}</p>
                        </div>

                        <div class="post-item-footer">
                            <a href="{{ route('landing.blog.show', $article->slug) }}" class="btn-default">Leer más</a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <p style="color:#666; font-size:1.1rem;">No hay artículos publicados aún.</p>
                </div>
                @endforelse
            </div>

            @if($articles->hasPages())
            <div class="row">
                <div class="col-md-12">
                    <div class="post-pagination wow fadeInUp" data-wow-delay="0.75s">
                        {{ $articles->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    <!-- Our blog Section End -->

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

