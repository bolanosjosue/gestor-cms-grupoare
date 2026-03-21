<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $appUrl = rtrim($appUrl ?? config('app.url'), '/');
        $canonical = $appUrl . '/blog/' . $article->slug . '/';
        $description = $metaDescription ?? '';

        $imageUrl = null;
        if ($article->cover_image_path) {
            $imageUrl = $appUrl . '/storage/' . ltrim($article->cover_image_path, '/');
        } elseif ($article->cover_image_url) {
            $imageUrl = $article->cover_image_url;
        }

        $publishedAt = $article->published_at ?? $article->updated_at;
        $day = optional($publishedAt)->format('d') ?: '--';
        $month = optional($publishedAt)->format('M') ?: '---';
        $readingMinutes = max(1, (int) ceil(str_word_count(strip_tags((string) $article->content)) / 200));

        $recommendedIds = $article->recommended_article_ids ?? [];
        if (!is_array($recommendedIds)) {
            $recommendedIds = json_decode($recommendedIds, true) ?: [];
        }
        $recommendedIds = array_values(array_filter($recommendedIds, fn($id) => $id && (int) $id !== (int) $article->id));
        $recommended = [];

        if (count($recommendedIds)) {
            $found = \App\Models\Article::whereIn('id', $recommendedIds)
                ->where('status', 'published')
                ->get()
                ->keyBy('id');

            foreach ($recommendedIds as $id) {
                if (isset($found[$id])) {
                    $recommended[] = $found[$id];
                }
            }
        }
    @endphp

    <meta name="description" content="{{ $description }}">
    <title>{{ $article->title }} | Grupo Are</title>
    <link rel="canonical" href="{{ $canonical }}" />
    <meta name="robots" content="{{ $article->status === 'published' ? 'index,follow' : 'noindex,nofollow' }}" />

    <meta property="og:type" content="article" />
    <meta property="og:title" content="{{ $article->title }}" />
    <meta property="og:description" content="{{ $description }}" />
    <meta property="og:url" content="{{ $canonical }}" />
    @if ($imageUrl)
        <meta property="og:image" content="{{ $imageUrl }}" />
    @endif

    <link rel="stylesheet" href="{{ $appUrl }}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ $appUrl }}/css/main.css">
    <link rel="stylesheet" href="{{ $appUrl }}/css/about.css">
    <link rel="stylesheet" href="{{ $appUrl }}/css/responsive.css">
    <link rel="stylesheet" href="{{ $appUrl }}/css/contact.css">
    <link rel="stylesheet" href="{{ $appUrl }}/css/blog-details.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">
</head>

<body>

    <header id="header" class="header-section">
        <div class="container">
            <nav class="navbar-custom">
                <a href="{{ $appUrl }}/" class="navbar-brand">
                    <img src="{{ $appUrl }}/images/logo.png" alt="Grupo Are">
                </a>

                <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
                    ☰
                </button>

                <div id="navmenu" class="mainmenu">
                    <ul class="nav-list">
                        <li><a href="{{ $appUrl }}/">Inicio</a></li>
                        <li><a href="{{ $appUrl }}/about">Nosotros</a></li>
                        <li><a href="{{ $appUrl }}/personal">Personal</a></li>
                        <li><a href="{{ $appUrl }}/ventas">Ventas</a></li>
                        <li><a href="{{ $appUrl }}/recursos">Eventos</a></li>
                        <li><a href="{{ $appUrl }}/contact">Contacto</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section Start -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="hero-wrap row align-items-center justify-content-center text-center">
                <div class="col-lg-10 col-md-10 col-12">
                    <div class="hero-content">
                        <h1 class="heading" style="font-size: 52px; line-height: 1.15;">{{ $article->title }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero Section End -->

    <!-- Blog Details Start -->
    <section class="blog-details">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div class="blog-details__left">
                        <div class="blog-details__img">
                            <img src="{{ $imageUrl ?: $appUrl . '/images/blog-1-1.jpg' }}" alt="{{ $article->cover_image_alt ?: $article->title }}">
                            <div class="blog-details__date">
                                <p>{{ $day }}<br>{{ $month }}</p>
                            </div>
                        </div>
                        <div class="blog-details__content">
                            <div class="blog-details__user-and-meta">
                                <div class="blog-details__user">
                                    <p><span class="icon-user-1"></span>Por Grupo Are</p>
                                </div>
                                <ul class="blog-details__meta list-unstyled">
                                    <li>
                                        <a href="#"><span class="fas fa-clock"></span>{{ $readingMinutes }} min de lectura</a>
                                    </li>
                                </ul>
                            </div>

                            <h3 class="blog-details__title">{{ $article->title }}</h3>

                            @if (!empty($article->excerpt))
                                <p class="blog-details__text-1">{{ $article->excerpt }}</p>
                            @endif

                            <div class="blog-details__text-2 content">
                                {!! $article->content !!}
                            </div>

                            @if (is_array($article->tags) && count($article->tags))
                                <div class="blog-details__tag-and-share">
                                    <div class="blog-details__tag">
                                        <h3 class="blog-details__tag-title">Tags :</h3>
                                        <ul class="blog-details__tag-list list-unstyled">
                                            @foreach ($article->tags as $tag)
                                                <li>
                                                    <a href="#">#{{ $tag }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-5">
                    <div class="sidebar">
                        @if (count($recommended))
                            <div class="sidebar__single sidebar__post wow fadeInUp" data-wow-delay=".1s">
                                <h3 class="sidebar__title">Artículos recomendados</h3>
                                <div class="sidebar__post-box">
                                    @foreach ($recommended as $r)
                                        @php
                                            $rImage = null;
                                            if ($r->cover_image_path) {
                                                $rImage = $appUrl . '/storage/' . ltrim($r->cover_image_path, '/');
                                            } elseif ($r->cover_image_url) {
                                                $rImage = $r->cover_image_url;
                                            }
                                        @endphp
                                        <div class="sidebar__post-single">
                                            <div class="sidebar-post__img">
                                                <img src="{{ $rImage ?: $appUrl . '/images/blog-1-1.jpg' }}" alt="{{ $r->title }}">
                                            </div>
                                            <div class="sidebar__post-content-box">
                                                <h3><a href="{{ $appUrl }}/blog/{{ $r->slug }}/">{{ $r->title }}</a></h3>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (is_array($article->tags) && count($article->tags))
                            <div class="sidebar__single sidebar__tags wow fadeInUp" data-wow-delay=".1s">
                                <h3 class="sidebar__title">Tags</h3>
                                <ul class="sidebar__tags-list clearfix list-unstyled">
                                    @foreach ($article->tags as $tag)
                                        <li><a href="#">{{ $tag }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Blog Details End -->

    <footer class="site-footer">
        <div class="container">
            <div class="row g-4 align-items-start">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand">
                        <img src="{{ $appUrl }}/images/logo.png" alt="Grupo Are" class="footer-logo">
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
                            <li><a href="{{ $appUrl }}/">Inicio</a></li>
                            <li><a href="{{ $appUrl }}/about">Nosotros</a></li>
                            <li><a href="{{ $appUrl }}/personal">Personal</a></li>
                            <li><a href="{{ $appUrl }}/ventas">Ventas</a></li>
                            <li><a href="{{ $appUrl }}/recursos">Eventos</a></li>
                            <li><a href="{{ $appUrl }}/contact">Contacto</a></li>
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
