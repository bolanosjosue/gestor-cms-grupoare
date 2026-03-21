<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin') - Grupo Are</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- TinyMCE editor -->
    <script src="https://cdn.tiny.cloud/1/72fxp7mkideo5krnt1km07lrwqmynpg5he82zrhk68pawg46/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">

</head>

<body>
    <div class="admin-shell">
        <aside class="admin-sidebar" id="adminSidebar" aria-label="Navegación del panel">
            <div class="sb-brand">
                <h1 class="sb-brand-name">Grupo <em>Are</em></h1>
                <p class="sb-brand-sub">Panel administrativo</p>
            </div>

            <div class="sb-search">
                <label for="sidebarNavSearch">Buscar sección</label>
                <input id="sidebarNavSearch" class="sb-search-input" type="text" placeholder="Dashboard, artículos...">
            </div>

            <nav class="sb-nav" id="sidebarNav">
                <p class="sb-nav-label">Contenido</p>
                <a href="{{ route('admin.dashboard') }}"
                    class="sb-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="sb-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 13h8V3H3zM13 21h8v-6h-8zM13 11h8V3h-8zM3 21h8v-6H3z"/>
                        </svg>
                    </span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.articles.index') }}"
                    class="sb-nav-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                    <span class="sb-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 4h16v16H4z"/>
                            <path d="M8 8h8M8 12h8M8 16h5"/>
                        </svg>
                    </span>
                    <span>Artículos</span>
                </a>

                <a href="{{ route('admin.staff.index') }}"
                    class="sb-nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                    <span class="sb-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="8.5" cy="7" r="3"/>
                            <path d="M20 8v6M23 11h-6"/>
                        </svg>
                    </span>
                    <span>Personal</span>
                </a>

                <a href="{{ route('admin.breeds.index') }}"
                    class="sb-nav-link {{ request()->routeIs('admin.breeds.*') ? 'active' : '' }}">
                    <span class="sb-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M5 12h14"/>
                            <path d="M8 9l-3 3 3 3"/>
                            <path d="M16 9l3 3-3 3"/>
                        </svg>
                    </span>
                    <span>Razas</span>
                </a>

                <a href="{{ route('admin.sales.index') }}"
                    class="sb-nav-link {{ request()->routeIs('admin.sales.*') ? 'active' : '' }}">
                    <span class="sb-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 7h18"/>
                            <path d="M6 7l1.5 12h9L18 7"/>
                            <path d="M9 10v6M12 10v6M15 10v6"/>
                        </svg>
                    </span>
                    <span>Ventas</span>
                </a>
            </nav>

            @auth
                <div class="sb-footer">
                    <div class="sb-user">
                        <div class="sb-avatar">{{ strtoupper(substr((string) auth()->user()->name, 0, 1)) }}</div>
                        <div class="sb-user-name">{{ auth()->user()->name }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="nav-logout">
                        @csrf
                        <button type="submit" class="sb-logout">Cerrar sesión</button>
                    </form>
                </div>
            @endauth
        </aside>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="admin-main-wrap">
            <header class="admin-topbar">
                <button class="hamburger" id="sidebarToggle" type="button" aria-label="Abrir menú">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <div class="topbar-brand">Grupo <span>Are</span> Admin</div>
            </header>

            <main class="admin-main">
                <div class="topbar">
                    <h2>@yield('title', 'Admin')</h2>
                    <div>@yield('actions')</div>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">Hay errores en el formulario. Revisá los campos marcados.</div>
                    <div class="alert alert-error">
                        <ul style="margin:0;padding-left:1.25rem;">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile sidebar
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggle = document.getElementById('sidebarToggle');
            const navSearch = document.getElementById('sidebarNavSearch');

            function closeSidebar() {
                if (!sidebar || !overlay || !toggle) return;
                sidebar.classList.remove('open');
                overlay.classList.remove('visible');
                toggle.classList.remove('open');
            }

            function openSidebar() {
                if (!sidebar || !overlay || !toggle) return;
                sidebar.classList.add('open');
                overlay.classList.add('visible');
                toggle.classList.add('open');
            }

            if (toggle) {
                toggle.addEventListener('click', function() {
                    if (sidebar && sidebar.classList.contains('open')) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            window.addEventListener('resize', function() {
                if (window.innerWidth > 991) closeSidebar();
            });

            // Sidebar nav filter
            if (navSearch) {
                navSearch.addEventListener('input', function() {
                    const q = navSearch.value.trim().toLowerCase();
                    document.querySelectorAll('#sidebarNav .sb-nav-link').forEach(function(link) {
                        const text = (link.textContent || '').toLowerCase();
                        link.style.display = !q || text.includes(q) ? '' : 'none';
                    });
                });
            }

            // Slug auto/manual
            document.querySelectorAll('[data-slug-source]').forEach(function(titleInput) {
                const form = titleInput.closest('form');
                if (!form) return;

                const slugInput = form.querySelector('[data-slug-input]');
                const autoCheckbox = form.querySelector('[data-slug-auto]');
                if (!slugInput || !autoCheckbox) return;

                function slugify(value) {
                    value = (value || '').toString();
                    value = value.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                    value = value.toLowerCase();
                    value = value.replace(/[^a-z0-9\s-]/g, '');
                    value = value.trim().replace(/\s+/g, '-');
                    value = value.replace(/-+/g, '-');
                    return value;
                }

                function applyAuto() {
                    if (!autoCheckbox.checked) return;
                    slugInput.value = slugify(titleInput.value);
                }

                titleInput.addEventListener('input', applyAuto);
                autoCheckbox.addEventListener('change', function() {
                    slugInput.readOnly = autoCheckbox.checked;
                    if (autoCheckbox.checked) applyAuto();
                });

                slugInput.readOnly = autoCheckbox.checked;
                if (autoCheckbox.checked && !slugInput.value) applyAuto();
            });

            // Tags input -> hidden JSON
            document.querySelectorAll('[data-tags-root]').forEach(function(root) {
                const input = root.querySelector('[data-tags-input]');
                const hidden = root.querySelector('[data-tags-hidden]');
                const list = root.querySelector('[data-tags-list]');
                const addBtn = root.querySelector('[data-tags-add]');

                if (!input || !hidden || !list || !addBtn) return;

                let tags = [];
                try {
                    const initial = JSON.parse(hidden.value || '[]');
                    if (Array.isArray(initial)) tags = initial;
                } catch (e) {}

                function render() {
                    list.innerHTML = '';
                    tags.forEach(function(t, idx) {
                        const el = document.createElement('span');
                        el.className = 'tag';
                        el.innerHTML =
                            '<span></span><button type="button" aria-label="Quitar tag">&times;</button>';
                        el.querySelector('span').textContent = t;
                        el.querySelector('button').addEventListener('click', function() {
                            tags.splice(idx, 1);
                            sync();
                        });
                        list.appendChild(el);
                    });
                    hidden.value = JSON.stringify(tags);
                }

                function normalizeTag(t) {
                    return (t || '').toString().trim().replace(/\s+/g, ' ');
                }

                function addTag() {
                    const t = normalizeTag(input.value);
                    if (!t) return;
                    if (tags.length >= 4) {
                        alert('Máximo 4 tags por artículo.');
                        return;
                    }
                    tags.push(t);
                    input.value = '';
                    sync();
                }

                function sync() {
                    render();
                }

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        addTag();
                    }
                });

                addBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    addTag();
                });

                render();
            });

            // TinyMCE initialization
            tinymce.init({
                selector: '#content',
                height: 400,
                plugins: 'autolink lists link image media table code help wordcount',
                toolbar: 'undo redo | formatselect | bold italic strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | link image media | code | help',
                menubar: 'file edit view insert format table help',
                language: 'es',
                content_style: 'body { font-family: system-ui, -apple-system, sans-serif; font-size: 14px; }',
                automatic_uploads: true
            });

            // Form validation para TinyMCE
            document.querySelectorAll('form').forEach(function(form) {
                const contentField = form.querySelector('#content');
                if (!contentField) return;

                form.addEventListener('submit', function(e) {
                    const editor = tinymce.get('content');
                    if (editor) {
                        const content = editor.getContent().trim();
                        if (!content) {
                            e.preventDefault();
                            alert(
                                'El contenido es obligatorio. Por favor, escriba algo en el editor.');
                            editor.focus();
                            return false;
                        }
                        // Copiar contenido de TinyMCE al textarea
                        contentField.value = content;
                    }
                });
            });
        });
    </script>
</body>

</html>
