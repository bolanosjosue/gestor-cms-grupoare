@extends('layouts.admin')

@section('title', 'Gestión de Animales')

@section('actions')
    <div class="row" style="gap:.5rem;">
        <button class="btn btn-primary" id="btn-agregar-animal" onclick="AnimalesApp.openFormModal()" style="display:none;">+ Agregar animal</button>
    </div>
@endsection

@section('content')
    {{-- ═══════════════ FILTROS ═══════════════ --}}
    <div class="animales-filters">
        <div class="animales-filters__grid">
            <div>
                <label for="filtro-agropecuaria">Agropecuaria</label>
                <select id="filtro-agropecuaria" onchange="AnimalesApp.applyFilters()">
                    <option value="">Todas</option>
                </select>
            </div>
            <div>
                <label for="filtro-estado">Estado</label>
                <select id="filtro-estado" onchange="AnimalesApp.applyFilters()">
                    <option value="">Todos</option>
                </select>
            </div>
            <div>
                <label for="filtro-reproductivo">Estado reproductivo</label>
                <select id="filtro-reproductivo" onchange="AnimalesApp.applyFilters()">
                    <option value="">Todos</option>
                </select>
            </div>
            <div>
                <label for="filtro-sexo">Sexo</label>
                <select id="filtro-sexo" onchange="AnimalesApp.applyFilters()">
                    <option value="">Todos</option>
                </select>
            </div>
            <div class="animales-filters__search">
                <label for="filtro-busqueda">Búsqueda</label>
                <input id="filtro-busqueda" type="text" placeholder="Nombre, código o ID electrónico..."
                       onkeydown="if(event.key==='Enter'){event.preventDefault();AnimalesApp.applyFilters();}">
            </div>
        </div>
        <div class="animales-filters__actions">
            <button class="btn btn-outline btn-small" onclick="AnimalesApp.clearFilters()">
                <i class="fa-solid fa-xmark"></i> Limpiar filtros
            </button>
            <button class="btn btn-primary btn-small" onclick="AnimalesApp.applyFilters()">
                <i class="fa-solid fa-magnifying-glass"></i> Buscar
            </button>
            <span id="filtros-activos-badge" class="pill pill-published" style="display:none;">Filtros activos</span>
        </div>
    </div>

    {{-- ═══════════════ TABLA ═══════════════ --}}
    <div id="animales-loading" class="animales-loading" style="display:none;">
        <div class="animales-spinner"></div>
        <span>Cargando datos...</span>
    </div>

    <div id="animales-table-wrap">
        <table id="animales-table">
            <thead>
            <tr>
                <th class="sortable" data-sort="codigo_practico" onclick="AnimalesApp.toggleSort('codigo_practico')">
                    Cód. práctico <span class="sort-icon"></span>
                </th>
                <th class="sortable" data-sort="nombre" onclick="AnimalesApp.toggleSort('nombre')">
                    Nombre <span class="sort-icon"></span>
                </th>
                <th class="sortable" data-sort="agropecuaria" onclick="AnimalesApp.toggleSort('agropecuaria')">
                    Agropecuaria <span class="sort-icon"></span>
                </th>
                <th class="sortable" data-sort="estado" onclick="AnimalesApp.toggleSort('estado')">
                    Estado <span class="sort-icon"></span>
                </th>
                <th class="sortable" data-sort="composicion_racial" onclick="AnimalesApp.toggleSort('composicion_racial')">
                    Composición racial <span class="sort-icon"></span>
                </th>
                <th class="sortable" data-sort="ultimo_peso" onclick="AnimalesApp.toggleSort('ultimo_peso')">
                    Peso (kg) <span class="sort-icon"></span>
                </th>
                <th class="sortable" data-sort="estado_reproductivo" onclick="AnimalesApp.toggleSort('estado_reproductivo')">
                    Est. reprod. <span class="sort-icon"></span>
                </th>
                <th class="sortable" data-sort="fecha_ultimo_parto" onclick="AnimalesApp.toggleSort('fecha_ultimo_parto')">
                    Últ. parto <span class="sort-icon"></span>
                </th>
                <th class="animales-table__th-actions">Acciones</th>
            </tr>
            </thead>
            <tbody id="animales-tbody">
            </tbody>
        </table>
        <p id="animales-empty" class="muted" style="margin:0;display:none;">No se encontraron animales.</p>
    </div>

    <div id="animales-pagination" class="mt-3" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;">
        <span id="animales-total" class="muted"></span>
        <div id="animales-pages"></div>
    </div>

    {{-- ═══════════════ MODAL CREAR/EDITAR ═══════════════ --}}
    <div id="animalFormModal" class="admin-modal-overlay" style="display:none;" onclick="if(event.target===this)AnimalesApp.closeFormModal();">
        <div class="admin-modal" style="max-width:780px;max-height:92vh;display:flex;flex-direction:column;">
            <div class="admin-modal__header">
                <h3 id="animalFormTitle">Agregar animal</h3>
                <button type="button" class="admin-modal__close" onclick="AnimalesApp.closeFormModal()">&times;</button>
            </div>
            <div class="admin-modal__body" style="overflow-y:auto;flex:1;">
                <input type="hidden" id="animalFormId" value="">

                {{-- Tabs --}}
                <div class="animales-tabs">
                    <button type="button" class="animales-tab active" data-tab="tab-ident" onclick="AnimalesApp.switchTab(this)">Identificación</button>
                    <button type="button" class="animales-tab" data-tab="tab-peso" onclick="AnimalesApp.switchTab(this)">Peso y producción</button>
                    <button type="button" class="animales-tab" data-tab="tab-repro" onclick="AnimalesApp.switchTab(this)">Reproducción</button>
                    <button type="button" class="animales-tab" data-tab="tab-gen" onclick="AnimalesApp.switchTab(this)">Genealogía</button>
                </div>

                {{-- Tab 1: Identificación --}}
                <div class="animales-tab-content active" id="tab-ident">
                    <div class="form-grid-2">
                        <div>
                            <label for="f-codigo_practico">Código práctico *</label>
                            <input id="f-codigo_practico" type="text" required>
                            <div class="field-error" id="err-codigo_practico"></div>
                        </div>
                        <div>
                            <label for="f-identificacion_electronica">Identificación electrónica *</label>
                            <input id="f-identificacion_electronica" type="text" maxlength="20" required>
                            <div class="field-error" id="err-identificacion_electronica"></div>
                        </div>
                        <div>
                            <label for="f-nombre">Nombre</label>
                            <input id="f-nombre" type="text">
                            <div class="field-error" id="err-nombre"></div>
                        </div>
                        <div>
                            <label for="f-sexo">Sexo</label>
                            <select id="f-sexo">
                                <option value="">— Seleccionar —</option>
                                <option value="Hembra">Hembra</option>
                                <option value="Macho">Macho</option>
                            </select>
                            <div class="field-error" id="err-sexo"></div>
                        </div>
                        <div>
                            <label for="f-agropecuaria">Agropecuaria *</label>
                            <input id="f-agropecuaria" type="text" list="list-agropecuarias" required>
                            <datalist id="list-agropecuarias"></datalist>
                            <div class="field-error" id="err-agropecuaria"></div>
                        </div>
                        <div>
                            <label for="f-estado">Estado *</label>
                            <input id="f-estado" type="text" list="list-estados" required>
                            <datalist id="list-estados"></datalist>
                            <div class="field-error" id="err-estado"></div>
                        </div>
                        <div class="span-2">
                            <label for="f-composicion_racial">Composición racial</label>
                            <input id="f-composicion_racial" type="text">
                            <div class="field-error" id="err-composicion_racial"></div>
                        </div>
                    </div>
                </div>

                {{-- Tab 2: Peso y producción --}}
                <div class="animales-tab-content" id="tab-peso">
                    <div class="form-grid-2">
                        <div>
                            <label for="f-ultimo_peso">Último peso (kg)</label>
                            <input id="f-ultimo_peso" type="number" step="0.01" min="0">
                            <div class="field-error" id="err-ultimo_peso"></div>
                        </div>
                        <div>
                            <label for="f-estandarizacion_produccion">Estandarización de producción</label>
                            <input id="f-estandarizacion_produccion" type="text">
                            <div class="field-error" id="err-estandarizacion_produccion"></div>
                        </div>
                        <div>
                            <label for="f-ultima_locacion">Última locación</label>
                            <input id="f-ultima_locacion" type="text">
                            <div class="field-error" id="err-ultima_locacion"></div>
                        </div>
                        <div>
                            <label for="f-clasificacion_asociacion">Clasificación asociación</label>
                            <input id="f-clasificacion_asociacion" type="text">
                            <div class="field-error" id="err-clasificacion_asociacion"></div>
                        </div>
                    </div>
                </div>

                {{-- Tab 3: Reproducción --}}
                <div class="animales-tab-content" id="tab-repro">
                    <div class="form-grid-2">
                        <div>
                            <label for="f-estado_reproductivo">Estado reproductivo</label>
                            <input id="f-estado_reproductivo" type="text" list="list-reproductivos">
                            <datalist id="list-reproductivos"></datalist>
                            <div class="field-error" id="err-estado_reproductivo"></div>
                        </div>
                        <div>
                            <label for="f-numero_revisiones">Número de revisiones</label>
                            <input id="f-numero_revisiones" type="number" min="0" step="1">
                            <div class="field-error" id="err-numero_revisiones"></div>
                        </div>
                        <div>
                            <label for="f-fecha_ultimo_servicio">Fecha último servicio</label>
                            <input id="f-fecha_ultimo_servicio" type="date">
                            <div class="field-error" id="err-fecha_ultimo_servicio"></div>
                        </div>
                        <div>
                            <label for="f-fecha_ultimo_parto">Fecha último parto</label>
                            <input id="f-fecha_ultimo_parto" type="date" max="{{ date('Y-m-d') }}">
                            <div class="field-error" id="err-fecha_ultimo_parto"></div>
                        </div>
                        <div>
                            <label for="f-fecha_secado">Fecha de secado</label>
                            <input id="f-fecha_secado" type="date" max="{{ date('Y-m-d') }}">
                            <div class="field-error" id="err-fecha_secado"></div>
                        </div>
                        <div>
                            <label for="f-codigo_reproductor">Código reproductor</label>
                            <input id="f-codigo_reproductor" type="text">
                            <div class="field-error" id="err-codigo_reproductor"></div>
                        </div>
                        <div>
                            <label for="f-codigo">Código</label>
                            <input id="f-codigo" type="text">
                            <div class="field-error" id="err-codigo"></div>
                        </div>
                        <div>
                            <label for="f-codigo_nombre">Código Nombre</label>
                            <input id="f-codigo_nombre" type="text">
                            <div class="field-error" id="err-codigo_nombre"></div>
                        </div>
                    </div>
                </div>

                {{-- Tab 4: Genealogía --}}
                <div class="animales-tab-content" id="tab-gen">
                    <div class="form-grid-2">
                        <div>
                            <label for="f-fecha_nacimiento">Fecha de nacimiento</label>
                            <input id="f-fecha_nacimiento" type="date">
                            <div class="field-error" id="err-fecha_nacimiento"></div>
                        </div>
                        <div>
                            <label for="f-padre_nombre">Padre: nombre del animal</label>
                            <input id="f-padre_nombre" type="text">
                            <div class="field-error" id="err-padre_nombre"></div>
                        </div>
                        <div>
                            <label for="f-codigo_madre">Código de la madre</label>
                            <input id="f-codigo_madre" type="text">
                            <div class="field-error" id="err-codigo_madre"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="admin-modal__footer">
                <button type="button" class="btn btn-outline" onclick="AnimalesApp.closeFormModal()">Cancelar</button>
                <button type="button" class="btn btn-primary" id="animalFormSubmit" onclick="AnimalesApp.submitForm()">
                    <span id="animalFormBtnText">Guardar</span>
                    <span id="animalFormBtnSpinner" class="animales-btn-spinner" style="display:none;"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════ MODAL ELIMINAR ═══════════════ --}}
    <div id="animalDeleteModal" class="admin-modal-overlay" style="display:none;" onclick="if(event.target===this)AnimalesApp.closeDeleteModal();">
        <div class="admin-modal" style="max-width:420px;">
            <div class="admin-modal__header">
                <h3>Eliminar animal</h3>
                <button type="button" class="admin-modal__close" onclick="AnimalesApp.closeDeleteModal()">&times;</button>
            </div>
            <div class="admin-modal__body">
                <p style="margin:0 0 .5rem;">¿Estás seguro de eliminar este animal?</p>
                <p style="margin:0;"><strong id="deleteAnimalInfo"></strong></p>
                <p class="muted" style="margin:.75rem 0 0;">Esta acción no se puede deshacer.</p>
            </div>
            <div class="admin-modal__footer">
                <button type="button" class="btn btn-outline" onclick="AnimalesApp.closeDeleteModal()">Cancelar</button>
                <button type="button" class="btn btn-danger" id="deleteConfirmBtn" onclick="AnimalesApp.confirmDelete()">
                    <span id="deleteBtnText">Eliminar</span>
                    <span id="deleteBtnSpinner" class="animales-btn-spinner" style="display:none;"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════ MODAL DETALLE ═══════════════ --}}
    <div id="animalDetailModal" class="admin-modal-overlay" style="display:none;" onclick="if(event.target===this)AnimalesApp.closeDetailModal();">
        <div class="admin-modal" style="max-width:640px;max-height:92vh;display:flex;flex-direction:column;">
            <div class="admin-modal__header">
                <h3 id="animalDetailTitle">Detalle del animal</h3>
                <button type="button" class="admin-modal__close" onclick="AnimalesApp.closeDetailModal()">&times;</button>
            </div>
            <div class="admin-modal__body animal-detail-modal__body">
                <div id="detail-loading" class="animal-detail-loading" style="display:none;">
                    <div class="animal-detail-loading__inner">
                        <div class="animales-spinner animal-detail-loading__spinner" aria-hidden="true"></div>
                        <span class="animal-detail-loading__text">Cargando datos…</span>
                    </div>
                </div>
                <div id="detail-content" class="animal-detail-content"></div>
            </div>
        </div>
    </div>

    <script>
    const AnimalesApp = (function() {
        const CSRF = '{{ csrf_token() }}';
        const BASE = '{{ route("admin.animales.index") }}';
        const FILTERS_URL = '{{ route("admin.animales.filtros") }}';
        const PERMISOS_URL = '{{ route("admin.permisos.mis") }}';
        const CAMPOS_URL = '{{ route("admin.permisos.campos") }}';

        let currentPage = 1;
        let currentSort = 'codigo_practico';
        let currentDir = 'asc';
        let filterOptions = {};
        let deleteId = null;
        let formDirty = false;

        // Permisos del usuario
        let permisos = { puede_ver: false, puede_agregar: false, puede_editar: false, puede_eliminar: false };
        let camposEditables = [];

        // Campos del formulario
        const FIELDS = [
            'codigo_practico','identificacion_electronica','nombre','sexo',
            'agropecuaria','estado','composicion_racial','ultimo_peso',
            'estandarizacion_produccion','ultima_locacion','clasificacion_asociacion',
            'estado_reproductivo','numero_revisiones','fecha_ultimo_servicio',
            'fecha_ultimo_parto','fecha_secado','codigo_reproductor','codigo',
            'codigo_nombre','fecha_nacimiento','padre_nombre','codigo_madre'
        ];

        // ─── INIT ───
        function init() {
            loadPermisos();
            loadFilterOptions();
            loadData();
            trackFormChanges();
        }

        function loadPermisos() {
            ajax(PERMISOS_URL, 'GET').then(function(data) {
                if (data.is_super_admin) {
                    permisos = { puede_ver: true, puede_agregar: true, puede_editar: true, puede_eliminar: true };
                } else if (data.permisos && data.permisos.animales) {
                    permisos = data.permisos.animales;
                }
                // Mostrar botón agregar si tiene permiso
                if (permisos.puede_agregar) {
                    var btnAgregar = document.getElementById('btn-agregar-animal');
                    if (btnAgregar) btnAgregar.style.display = '';
                }
            }).catch(function() {});

            ajax(CAMPOS_URL, 'GET').then(function(data) {
                if (data.campos_editables) {
                    camposEditables = data.campos_editables;
                }
            }).catch(function() {});
        }

        function trackFormChanges() {
            FIELDS.forEach(function(f) {
                var el = document.getElementById('f-' + f);
                if (el) el.addEventListener('input', function() { formDirty = true; });
            });
        }

        // ─── AJAX HELPER ───
        function ajax(url, method, data) {
            var opts = {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            };
            if (data) {
                opts.headers['Content-Type'] = 'application/json';
                opts.body = JSON.stringify(data);
            }
            return fetch(url, opts).then(function(r) {
                if (!r.ok) return r.json().then(function(e) { throw e; });
                return r.json();
            });
        }

        // ─── FILTER OPTIONS ───
        function loadFilterOptions() {
            ajax(FILTERS_URL, 'GET').then(function(data) {
                filterOptions = data;
                populateSelect('filtro-agropecuaria', data.agropecuarias);
                populateSelect('filtro-estado', data.estados);
                populateSelect('filtro-reproductivo', data.estados_reproductivos);
                populateSelect('filtro-sexo', data.sexos);
                populateDatalist('list-agropecuarias', data.agropecuarias);
                populateDatalist('list-estados', data.estados);
                populateDatalist('list-reproductivos', data.estados_reproductivos);
            }).catch(function() {});
        }

        function populateSelect(id, items) {
            var sel = document.getElementById(id);
            if (!sel || !items) return;
            var firstOption = sel.options[0];
            sel.innerHTML = '';
            sel.appendChild(firstOption);
            items.forEach(function(v) {
                var o = document.createElement('option');
                o.value = v; o.textContent = v;
                sel.appendChild(o);
            });
        }

        function populateDatalist(id, items) {
            var dl = document.getElementById(id);
            if (!dl || !items) return;
            dl.innerHTML = '';
            items.forEach(function(v) {
                var o = document.createElement('option');
                o.value = v;
                dl.appendChild(o);
            });
        }

        // ─── LOAD DATA ───
        function loadData() {
            showLoading(true);
            var params = new URLSearchParams();
            params.set('page', currentPage);
            params.set('sort', currentSort);
            params.set('dir', currentDir);

            var agro = document.getElementById('filtro-agropecuaria').value;
            var est = document.getElementById('filtro-estado').value;
            var rep = document.getElementById('filtro-reproductivo').value;
            var sex = document.getElementById('filtro-sexo').value;
            var bus = document.getElementById('filtro-busqueda').value.trim();

            if (agro) params.set('agropecuaria', agro);
            if (est) params.set('estado', est);
            if (rep) params.set('estado_reproductivo', rep);
            if (sex) params.set('sexo', sex);
            if (bus) params.set('busqueda', bus);

            // Mostrar badge de filtros activos
            var hasFilters = agro || est || rep || sex || bus;
            document.getElementById('filtros-activos-badge').style.display = hasFilters ? 'inline-flex' : 'none';

            ajax(BASE + '?' + params.toString(), 'GET').then(function(res) {
                renderTable(res.data);
                renderPagination(res.current_page, res.last_page, res.total);
                showLoading(false);
            }).catch(function(err) {
                showLoading(false);
                alert('Error al cargar los datos: ' + (err.message || 'Error desconocido'));
            });
        }

        function showLoading(show) {
            document.getElementById('animales-loading').style.display = show ? 'flex' : 'none';
            document.getElementById('animales-table-wrap').style.opacity = show ? '0.4' : '1';
        }

        // ─── RENDER TABLE ───
        function renderTable(items) {
            var tbody = document.getElementById('animales-tbody');
            var empty = document.getElementById('animales-empty');
            tbody.innerHTML = '';

            if (!items || items.length === 0) {
                empty.style.display = 'block';
                document.getElementById('animales-table').style.display = 'none';
                return;
            }

            empty.style.display = 'none';
            document.getElementById('animales-table').style.display = '';

            items.forEach(function(a) {
                var tr = document.createElement('tr');
                var actionsHtml = '<button type="button" class="btn btn-info btn-small animales-actions__btn" onclick="AnimalesApp.openDetailModal(' + a.id + ')" title="Ver detalle"><i class="fa-solid fa-eye"></i></button>';
                if (permisos.puede_editar) {
                    actionsHtml += '<button type="button" class="btn btn-outline btn-small animales-actions__btn" onclick="AnimalesApp.openFormModal(' + a.id + ')" title="Editar"><i class="fa-solid fa-pen"></i></button>';
                }
                if (permisos.puede_eliminar) {
                    actionsHtml += '<button type="button" class="btn btn-danger btn-small animales-actions__btn" onclick="AnimalesApp.openDeleteModal(' + a.id + ',\'' + esc(a.nombre || '') + '\',\'' + esc(a.codigo_practico) + '\')" title="Eliminar"><i class="fa-solid fa-trash"></i></button>';
                }
                tr.innerHTML =
                    '<td><strong>' + esc(a.codigo_practico) + '</strong></td>' +
                    '<td>' + esc(a.nombre || '—') + '</td>' +
                    '<td>' + esc(a.agropecuaria || '—') + '</td>' +
                    '<td>' + esc(a.estado || '—') + '</td>' +
                    '<td>' + esc(a.composicion_racial || '—') + '</td>' +
                    '<td>' + (a.ultimo_peso != null ? esc(a.ultimo_peso) : '—') + '</td>' +
                    '<td>' + esc(a.estado_reproductivo || '—') + '</td>' +
                    '<td>' + formatDate(a.fecha_ultimo_parto) + '</td>' +
                    '<td class="animales-table__cell-actions">' +
                        '<div class="animales-actions">' + actionsHtml + '</div>' +
                    '</td>';
                tbody.appendChild(tr);
            });

            // Actualizar iconos de sort
            document.querySelectorAll('#animales-table th.sortable').forEach(function(th) {
                var icon = th.querySelector('.sort-icon');
                if (th.dataset.sort === currentSort) {
                    icon.textContent = currentDir === 'asc' ? ' ▲' : ' ▼';
                    th.classList.add('sorted');
                } else {
                    icon.textContent = '';
                    th.classList.remove('sorted');
                }
            });
        }

        // ─── DETAIL MODAL ───
        function openDetailModal(id) {
            document.getElementById('detail-loading').style.display = 'flex';
            document.getElementById('detail-content').innerHTML = '';
            document.getElementById('animalDetailModal').style.display = 'flex';
            ajax(BASE + '/' + id, 'GET').then(function(animal) {
                document.getElementById('detail-loading').style.display = 'none';
                document.getElementById('animalDetailTitle').textContent = animal.nombre || animal.codigo_practico || 'Detalle del animal';
                let html = '';
                const sections = [
                    { title: 'Identificación', icon: 'fa-tag', fields: [
                        ['codigo_practico', 'Código práctico'],
                        ['identificacion_electronica', 'ID electrónica'],
                        ['nombre', 'Nombre'],
                        ['sexo', 'Sexo'],
                        ['agropecuaria', 'Agropecuaria'],
                        ['estado', 'Estado'],
                        ['composicion_racial', 'Composición racial'],
                    ]},
                    { title: 'Peso y producción', icon: 'fa-weight-scale', fields: [
                        ['ultimo_peso', 'Último peso (kg)'],
                        ['estandarizacion_produccion', 'Estandarización de producción'],
                        ['ultima_locacion', 'Última locación'],
                        ['clasificacion_asociacion', 'Clasificación asociación'],
                    ]},
                    { title: 'Reproducción', icon: 'fa-heart', fields: [
                        ['estado_reproductivo', 'Estado reproductivo'],
                        ['numero_revisiones', 'Número de revisiones'],
                        ['fecha_ultimo_servicio', 'Fecha último servicio'],
                        ['fecha_ultimo_parto', 'Fecha último parto'],
                        ['fecha_secado', 'Fecha de secado'],
                        ['codigo_reproductor', 'Código reproductor'],
                        ['codigo', 'Código'],
                        ['codigo_nombre', 'Código nombre'],
                    ]},
                    { title: 'Genealogía', icon: 'fa-sitemap', fields: [
                        ['fecha_nacimiento', 'Fecha de nacimiento'],
                        ['padre_nombre', 'Padre: nombre del animal'],
                        ['codigo_madre', 'Código de la madre'],
                    ]},
                ];
                const dateFields = ['fecha_nacimiento','fecha_ultimo_servicio','fecha_ultimo_parto','fecha_secado'];
                sections.forEach(function(section) {
                    html += '<div class="detail-section">';
                    html += '<h4><i class="fa-solid ' + section.icon + '"></i> ' + section.title + '</h4>';
                    html += '<div class="detail-fields">';
                    section.fields.forEach(function(field) {
                        let key = field[0], label = field[1];
                        let val = animal[key];
                        if (dateFields.indexOf(key) !== -1 && val) {
                            val = formatDate(val);
                        }
                        if (!val) val = '—';
                        html += '<div class="detail-field"><span class="detail-label">' + label + ':</span> <span class="detail-value">' + esc(val) + '</span></div>';
                    });
                    html += '</div></div>';
                });
                document.getElementById('detail-content').innerHTML = html;
            }).catch(function() {
                document.getElementById('detail-loading').style.display = 'none';
                document.getElementById('detail-content').innerHTML = '<p class="muted">Error al cargar los datos.</p>';
            });
        }

        function closeDetailModal() {
            document.getElementById('animalDetailModal').style.display = 'none';
        }

        function renderPagination(current, last, total) {
            document.getElementById('animales-total').textContent = total + ' registros';
            var wrap = document.getElementById('animales-pages');
            wrap.innerHTML = '';

            if (last <= 1) return;

            function btn(page, label, active, disabled) {
                var el = document.createElement(disabled ? 'span' : 'a');
                el.textContent = label;
                el.href = '#';
                el.className = active ? 'animales-page active' : 'animales-page';
                if (disabled) el.classList.add('disabled');
                if (!disabled && !active) {
                    el.addEventListener('click', function(e) {
                        e.preventDefault();
                        currentPage = page;
                        loadData();
                    });
                }
                wrap.appendChild(el);
            }

            btn(current - 1, '‹', false, current === 1);
            for (var i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
                btn(i, i, i === current, false);
            }
            btn(current + 1, '›', false, current === last);
        }

        function esc(str) {
            if (str == null) return '';
            var div = document.createElement('div');
            div.textContent = String(str);
            return div.innerHTML;
        }

        function formatDate(d) {
            if (!d) return '—';
            var date = null;
            if (typeof d === 'string') {
                var s = d.trim();
                if (/^\d{4}-\d{2}-\d{2}/.test(s)) {
                    var ymd = s.substring(0, 10).split('-');
                    date = new Date(parseInt(ymd[0], 10), parseInt(ymd[1], 10) - 1, parseInt(ymd[2], 10));
                } else {
                    date = new Date(s);
                }
            } else {
                date = new Date(d);
            }
            if (!date || isNaN(date.getTime())) return '—';
            return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' });
        }

        // ─── SORTING ───
        function toggleSort(field) {
            if (currentSort === field) {
                currentDir = currentDir === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort = field;
                currentDir = 'asc';
            }
            currentPage = 1;
            loadData();
        }

        // ─── FILTERS ───
        function applyFilters() {
            currentPage = 1;
            loadData();
        }

        function clearFilters() {
            document.getElementById('filtro-agropecuaria').value = '';
            document.getElementById('filtro-estado').value = '';
            document.getElementById('filtro-reproductivo').value = '';
            document.getElementById('filtro-sexo').value = '';
            document.getElementById('filtro-busqueda').value = '';
            currentPage = 1;
            loadData();
        }

        // ─── FORM MODAL (CREATE / EDIT) ───
        function openFormModal(id) {
            formDirty = false;
            clearFormErrors();

            // Aplicar restricciones de campos editables
            applyFieldPermissions(!!id);

            if (id) {
                document.getElementById('animalFormTitle').textContent = 'Editar animal';
                document.getElementById('animalFormBtnText').textContent = 'Actualizar';
                document.getElementById('animalFormId').value = id;

                showLoading(true);
                ajax(BASE + '/' + id, 'GET').then(function(animal) {
                    populateForm(animal);
                }).catch(function() {
                    alert('No se pudieron cargar los datos del animal.');
                }).finally(function() {
                    showLoading(false);
                });
            } else {
                document.getElementById('animalFormTitle').textContent = 'Agregar animal';
                document.getElementById('animalFormBtnText').textContent = 'Guardar';
                document.getElementById('animalFormId').value = '';
                FIELDS.forEach(function(f) {
                    var el = document.getElementById('f-' + f);
                    if (el) el.value = '';
                });
            }

            document.getElementById('animalFormModal').style.display = 'flex';
            // Activar primer tab
            switchTab(document.querySelector('.animales-tab[data-tab="tab-ident"]'));
            setTimeout(function() {
                document.getElementById('f-codigo_practico').focus();
            }, 80);
        }

        function populateForm(animal) {
            FIELDS.forEach(function(f) {
                var el = document.getElementById('f-' + f);
                if (!el) return;
                var val = animal[f];
                if (val == null) val = '';
                // Fechas: asegurar formato YYYY-MM-DD
                if (f.startsWith('fecha_') && val && val.includes('T')) {
                    val = val.split('T')[0];
                }
                el.value = val;
            });
            document.getElementById('animalFormId').value = animal.id;
            formDirty = false;
        }

        function closeFormModal() {
            if (formDirty) {
                if (!confirm('Hay cambios sin guardar. ¿Deseas cerrar de todos modos?')) return;
            }
            document.getElementById('animalFormModal').style.display = 'none';
            formDirty = false;
        }

        function switchTab(btn) {
            document.querySelectorAll('.animales-tab').forEach(function(t) { t.classList.remove('active'); });
            document.querySelectorAll('.animales-tab-content').forEach(function(c) { c.classList.remove('active'); });
            btn.classList.add('active');
            document.getElementById(btn.dataset.tab).classList.add('active');
        }

        function clearFormErrors() {
            document.querySelectorAll('#animalFormModal .field-error').forEach(function(e) { e.textContent = ''; });
        }

        function applyFieldPermissions(isEdit) {
            FIELDS.forEach(function(f) {
                var el = document.getElementById('f-' + f);
                if (!el) return;
                if (isEdit && camposEditables.length > 0 && camposEditables.indexOf(f) === -1) {
                    el.disabled = true;
                    el.style.opacity = '0.5';
                    el.title = 'No tienes permiso para editar este campo';
                } else {
                    el.disabled = false;
                    el.style.opacity = '';
                    el.title = '';
                }
            });
        }

        function submitForm() {
            clearFormErrors();
            var id = document.getElementById('animalFormId').value;
            var data = {};

            FIELDS.forEach(function(f) {
                var el = document.getElementById('f-' + f);
                if (!el) return;
                var v = el.value.trim();
                if (v === '') v = null;
                data[f] = v;
            });

            // Validación local
            var hasError = false;
            function setErr(field, msg) {
                var el = document.getElementById('err-' + field);
                if (el) el.textContent = msg;
                hasError = true;
            }

            if (!data.codigo_practico) setErr('codigo_practico', 'Este campo es requerido.');
            if (!data.identificacion_electronica) setErr('identificacion_electronica', 'Este campo es requerido.');
            if (data.identificacion_electronica && !/^\d{1,20}$/.test(data.identificacion_electronica)) {
                setErr('identificacion_electronica', 'Solo números, máximo 20 caracteres.');
            }
            if (!data.agropecuaria) setErr('agropecuaria', 'Este campo es requerido.');
            if (!data.estado) setErr('estado', 'Este campo es requerido.');

            if (hasError) {
                // Mostrar tab con el primer error
                var firstErr = document.querySelector('#animalFormModal .field-error:not(:empty)');
                if (firstErr) {
                    var tabContent = firstErr.closest('.animales-tab-content');
                    if (tabContent) {
                        var tabBtn = document.querySelector('.animales-tab[data-tab="' + tabContent.id + '"]');
                        if (tabBtn) switchTab(tabBtn);
                    }
                }
                return;
            }

            var url = id ? BASE + '/' + id : BASE;
            var method = id ? 'PUT' : 'POST';

            document.getElementById('animalFormBtnText').style.display = 'none';
            document.getElementById('animalFormBtnSpinner').style.display = 'inline-block';
            document.getElementById('animalFormSubmit').disabled = true;

            ajax(url, method, data).then(function(res) {
                formDirty = false;
                document.getElementById('animalFormModal').style.display = 'none';
                loadData();
                loadFilterOptions(); // Actualizar opciones de filtro
            }).catch(function(err) {
                if (err.errors) {
                    Object.keys(err.errors).forEach(function(field) {
                        var el = document.getElementById('err-' + field);
                        if (el) el.textContent = err.errors[field][0];
                    });
                    // Mostrar tab con error
                    var firstErr = document.querySelector('#animalFormModal .field-error:not(:empty)');
                    if (firstErr) {
                        var tabContent = firstErr.closest('.animales-tab-content');
                        if (tabContent) {
                            var tabBtn = document.querySelector('.animales-tab[data-tab="' + tabContent.id + '"]');
                            if (tabBtn) switchTab(tabBtn);
                        }
                    }
                } else {
                    alert('Error al guardar: ' + (err.message || 'Error desconocido'));
                }
            }).finally(function() {
                document.getElementById('animalFormBtnText').style.display = '';
                document.getElementById('animalFormBtnSpinner').style.display = 'none';
                document.getElementById('animalFormSubmit').disabled = false;
            });
        }

        // ─── DELETE MODAL ───
        function openDeleteModal(id, nombre, codigo) {
            deleteId = id;
            document.getElementById('deleteAnimalInfo').textContent =
                (nombre || 'Sin nombre') + ' — Código: ' + (codigo || 'N/A');
            document.getElementById('animalDeleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('animalDeleteModal').style.display = 'none';
            deleteId = null;
        }

        function confirmDelete() {
            if (!deleteId) return;

            document.getElementById('deleteBtnText').style.display = 'none';
            document.getElementById('deleteBtnSpinner').style.display = 'inline-block';
            document.getElementById('deleteConfirmBtn').disabled = true;

            ajax(BASE + '/' + deleteId, 'DELETE').then(function() {
                closeDeleteModal();
                loadData();
            }).catch(function(err) {
                alert('Error al eliminar: ' + (err.message || 'Error desconocido'));
            }).finally(function() {
                document.getElementById('deleteBtnText').style.display = '';
                document.getElementById('deleteBtnSpinner').style.display = 'none';
                document.getElementById('deleteConfirmBtn').disabled = false;
            });
        }

        // Init
        document.addEventListener('DOMContentLoaded', init);

        return {
            applyFilters: applyFilters,
            clearFilters: clearFilters,
            toggleSort: toggleSort,
            openFormModal: openFormModal,
            closeFormModal: closeFormModal,
            submitForm: submitForm,
            switchTab: switchTab,
            openDetailModal: openDetailModal,
            closeDetailModal: closeDetailModal,
            openDeleteModal: openDeleteModal,
            closeDeleteModal: closeDeleteModal,
            confirmDelete: confirmDelete,
        };
    })();
    </script>
@endsection

