@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')

@section('actions')
    <div class="row" style="gap:.5rem;">
        <button class="btn btn-primary" onclick="PersonalApp.openModal()">+ Agregar usuario</button>
    </div>
@endsection

@section('content')
    {{-- ═══════════════ FILTROS ═══════════════ --}}
    <div style="margin-bottom:1rem;display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
        <label for="filtro-estado-user" style="font-weight:600;font-size:.85rem;">Estado:</label>
        <select id="filtro-estado-user" onchange="PersonalApp.loadUsers()" style="padding:.35rem .6rem;border-radius:6px;border:1px solid #d0cfcb;">
            <option value="">Todos</option>
            <option value="activo">Activos</option>
            <option value="inactivo">Inactivos</option>
        </select>
    </div>

    {{-- ═══════════════ TABLA ═══════════════ --}}
    <div id="users-loading" class="animales-loading" style="display:none;">
        <div class="animales-spinner"></div>
        <span>Cargando usuarios...</span>
    </div>

    <table id="users-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Módulos</th>
                <th>Estado</th>
                <th style="width:160px;">Acciones</th>
            </tr>
        </thead>
        <tbody id="users-tbody"></tbody>
    </table>
    <p id="users-empty" class="muted" style="display:none;">No se encontraron usuarios.</p>

    {{-- ═══════════════ MODAL CREAR/EDITAR USUARIO ═══════════════ --}}
    <div id="userModal" class="admin-modal-overlay" style="display:none;" onclick="if(event.target===this)PersonalApp.closeModal();">
        <div class="admin-modal" style="max-width:820px;max-height:92vh;display:flex;flex-direction:column;">
            <div class="admin-modal__header">
                <h3 id="userModalTitle">Agregar usuario</h3>
                <button type="button" class="admin-modal__close" onclick="PersonalApp.closeModal()">&times;</button>
            </div>

            {{-- Step indicator --}}
            <div class="wizard-steps" id="wizardSteps">
                <div class="wizard-step active" data-step="1"><span class="wizard-step__num">1</span><span class="wizard-step__label">Datos</span></div>
                <div class="wizard-step" data-step="2"><span class="wizard-step__num">2</span><span class="wizard-step__label">Módulos</span></div>
                <div class="wizard-step" data-step="3"><span class="wizard-step__num">3</span><span class="wizard-step__label">Campos</span></div>
            </div>

            <div class="admin-modal__body" style="overflow-y:auto;flex:1;">
                <input type="hidden" id="userId" value="">

                {{-- ══════ STEP 1: Datos del usuario ══════ --}}
                <div class="wizard-panel active" id="step-1">
                    <div class="form-grid-2">
                        <div>
                            <label for="u-name">Nombre *</label>
                            <input id="u-name" type="text" required>
                            <div class="field-error" id="err-name"></div>
                        </div>
                        <div>
                            <label for="u-username">Nombre de usuario *</label>
                            <input id="u-username" type="text" required autocomplete="off" placeholder="Ej: juan.perez">
                            <div class="field-error" id="err-username"></div>
                        </div>
                        <div>
                            <label for="u-password">Contraseña <span id="pwdHint">*</span></label>
                            <input id="u-password" type="password" autocomplete="new-password">
                            <div class="field-error" id="err-password"></div>
                        </div>
                        <div>
                            <label for="u-password_confirmation">Confirmar contraseña</label>
                            <input id="u-password_confirmation" type="password" autocomplete="new-password">
                        </div>
                        <div>
                            <label for="u-rol">Tipo de usuario</label>
                            <select id="u-rol" onchange="PersonalApp.onRolChange()">
                                <option value="">Personalizado (permisos específicos)</option>
                                <option value="encargado">Encargado (solo App: Pesajes)</option>
                                <option value="veterinario">Veterinario (solo App: Veterinario)</option>
                                <option value="supervisor">Supervisor (solo App: Pesos)</option>
                            </select>
                        </div>
                        <div style="display:flex;align-items:center;gap:.75rem;padding-top:1.2rem;">
                            <label class="toggle-label">
                                <input type="checkbox" id="u-activo" checked>
                                <span class="toggle-switch"></span>
                            </label>
                            <span style="font-weight:600;font-size:.85rem;">Usuario activo</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:.75rem;padding-top:1.2rem;">
                            <label class="toggle-label">
                                <input type="checkbox" id="u-is_super_admin" onchange="PersonalApp.onSuperAdminChange()">
                                <span class="toggle-switch"></span>
                            </label>
                            <span style="font-weight:600;font-size:.85rem;">Super Admin</span>
                        </div>
                    </div>
                    <div id="superAdminMsg" class="wizard-info-msg" style="display:none;">
                        <i class="fa-solid fa-shield-halved"></i>
                        Los Super Admin tienen acceso completo a todo el sistema. No es necesario configurar permisos.
                    </div>
                    <div id="rolMsg" class="wizard-info-msg" style="display:none;">
                        <i class="fa-solid fa-id-badge"></i>
                        <span id="rolMsgText"></span>
                    </div>
                </div>

                {{-- ══════ STEP 2: Permisos de módulos ══════ --}}
                <div class="wizard-panel" id="step-2">
                    <p style="margin:0 0 1rem;font-size:.85rem;" class="muted">Configura los permisos de acceso para cada módulo del sistema.</p>
                    <table class="permisos-table" id="permisosTable">
                        <thead>
                            <tr>
                                <th>Módulo</th>
                                <th style="text-align:center;width:70px;">Ver</th>
                                <th style="text-align:center;width:70px;">Agregar</th>
                                <th style="text-align:center;width:70px;">Editar</th>
                                <th style="text-align:center;width:70px;">Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-modulo="dashboard">
                                <td><i class="fa-solid fa-chart-pie"></i> Dashboard</td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="dashboard" data-accion="puede_ver" onchange="PersonalApp.onPermChange(this)"></td>
                                <td style="text-align:center;"><span class="muted">—</span></td>
                                <td style="text-align:center;"><span class="muted">—</span></td>
                                <td style="text-align:center;"><span class="muted">—</span></td>
                            </tr>
                            <tr data-modulo="articulos">
                                <td><i class="fa-solid fa-newspaper"></i> Artículos</td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="articulos" data-accion="puede_ver" onchange="PersonalApp.onPermChange(this)"></td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="articulos" data-accion="puede_agregar" onchange="PersonalApp.onPermChange(this)"></td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="articulos" data-accion="puede_editar" onchange="PersonalApp.onPermChange(this)"></td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="articulos" data-accion="puede_eliminar" onchange="PersonalApp.onPermChange(this)"></td>
                            </tr>
                            <tr data-modulo="personal">
                                <td><i class="fa-solid fa-user-group"></i> Personal</td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="personal" data-accion="puede_ver" onchange="PersonalApp.onPermChange(this)"></td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="personal" data-accion="puede_agregar" onchange="PersonalApp.onPermChange(this)"></td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="personal" data-accion="puede_editar" onchange="PersonalApp.onPermChange(this)"></td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="personal" data-accion="puede_eliminar" onchange="PersonalApp.onPermChange(this)"></td>
                            </tr>
                            <tr data-modulo="razas">
                                <td><i class="fa-solid fa-dna"></i> Razas</td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="razas" data-accion="puede_ver" onchange="PersonalApp.onPermChange(this)"></td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="razas" data-accion="puede_agregar" onchange="PersonalApp.onPermChange(this)"></td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="razas" data-accion="puede_editar" onchange="PersonalApp.onPermChange(this)"></td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="razas" data-accion="puede_eliminar" onchange="PersonalApp.onPermChange(this)"></td>
                            </tr>
                            <tr data-modulo="ventas">
                                <td><i class="fa-solid fa-cart-shopping"></i> Ventas</td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="ventas" data-accion="puede_ver" onchange="PersonalApp.onPermChange(this)"></td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="ventas" data-accion="puede_agregar" onchange="PersonalApp.onPermChange(this)"></td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="ventas" data-accion="puede_editar" onchange="PersonalApp.onPermChange(this)"></td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="ventas" data-accion="puede_eliminar" onchange="PersonalApp.onPermChange(this)"></td>
                            </tr>
                            <tr data-modulo="animales">
                                <td><i class="fa-solid fa-paw"></i> Animales</td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="animales" data-accion="puede_ver" onchange="PersonalApp.onPermChange(this)"></td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="animales" data-accion="puede_agregar" onchange="PersonalApp.onPermChange(this)"></td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="animales" data-accion="puede_editar" onchange="PersonalApp.onPermChange(this)"></td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="animales" data-accion="puede_eliminar" onchange="PersonalApp.onPermChange(this)"></td>
                            </tr>
                            <tr data-modulo="auditoria">
                                <td><i class="fa-solid fa-file-lines"></i> Auditoría</td>
                                <td style="text-align:center;"><input type="checkbox" class="perm-cb" data-modulo="auditoria" data-accion="puede_ver" onchange="PersonalApp.onPermChange(this)"></td>
                                <td style="text-align:center;"><span class="muted">—</span></td>
                                <td style="text-align:center;"><span class="muted">—</span></td>
                                <td style="text-align:center;"><span class="muted">—</span></td>
                            </tr>
                        </tbody>
                    </table>
                    <div id="configCamposHint" style="display:none;margin-top:.75rem;">
                        <button type="button" class="btn btn-outline btn-small" onclick="PersonalApp.goToStep(3)">
                            <i class="fa-solid fa-sliders"></i> Configurar campos editables en Animales →
                        </button>
                    </div>
                </div>

                {{-- ══════ STEP 3: Campos editables de animales ══════ --}}
                <div class="wizard-panel" id="step-3">
                    <p style="margin:0 0 .75rem;font-size:.85rem;" class="muted">Selecciona qué campos del módulo de animales puede editar este usuario.</p>

                    <div class="campos-section">
                        <div class="campos-section__header">
                            <strong><i class="fa-solid fa-tag"></i> Identificación</strong>
                            <div>
                                <button type="button" class="btn-link" onclick="PersonalApp.toggleSection(this, true)">Todos</button>
                                <button type="button" class="btn-link" onclick="PersonalApp.toggleSection(this, false)">Ninguno</button>
                            </div>
                        </div>
                        <div class="campos-grid">
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="codigo_practico"><span>Código práctico</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="identificacion_electronica"><span>ID electrónica</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="nombre"><span>Nombre</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="sexo"><span>Sexo</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="agropecuaria"><span>Agropecuaria</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="estado"><span>Estado</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="composicion_racial"><span>Composición racial</span></label>
                        </div>
                    </div>

                    <div class="campos-section">
                        <div class="campos-section__header">
                            <strong><i class="fa-solid fa-weight-scale"></i> Peso y producción</strong>
                            <div>
                                <button type="button" class="btn-link" onclick="PersonalApp.toggleSection(this, true)">Todos</button>
                                <button type="button" class="btn-link" onclick="PersonalApp.toggleSection(this, false)">Ninguno</button>
                            </div>
                        </div>
                        <div class="campos-grid">
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="ultimo_peso"><span>Último peso</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="estandarizacion_produccion"><span>Estandarización producción</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="ultima_locacion"><span>Última locación</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="clasificacion_asociacion"><span>Clasificación asociación</span></label>
                        </div>
                    </div>

                    <div class="campos-section">
                        <div class="campos-section__header">
                            <strong><i class="fa-solid fa-heart"></i> Reproducción</strong>
                            <div>
                                <button type="button" class="btn-link" onclick="PersonalApp.toggleSection(this, true)">Todos</button>
                                <button type="button" class="btn-link" onclick="PersonalApp.toggleSection(this, false)">Ninguno</button>
                            </div>
                        </div>
                        <div class="campos-grid">
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="estado_reproductivo"><span>Estado reproductivo</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="numero_revisiones"><span>Nro. revisiones</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="fecha_ultimo_servicio"><span>Fecha último servicio</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="fecha_ultimo_parto"><span>Fecha último parto</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="fecha_secado"><span>Fecha de secado</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="codigo_reproductor"><span>Código reproductor</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="codigo"><span>Código</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="codigo_nombre"><span>Código nombre</span></label>
                        </div>
                    </div>

                    <div class="campos-section">
                        <div class="campos-section__header">
                            <strong><i class="fa-solid fa-sitemap"></i> Genealogía</strong>
                            <div>
                                <button type="button" class="btn-link" onclick="PersonalApp.toggleSection(this, true)">Todos</button>
                                <button type="button" class="btn-link" onclick="PersonalApp.toggleSection(this, false)">Ninguno</button>
                            </div>
                        </div>
                        <div class="campos-grid">
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="fecha_nacimiento"><span>Fecha de nacimiento</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="padre_nombre"><span>Padre: nombre</span></label>
                            <label class="campo-toggle"><input type="checkbox" class="campo-cb" data-campo="codigo_madre"><span>Código de la madre</span></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-modal__footer" style="justify-content:space-between;">
                <button type="button" class="btn btn-outline" id="wizardPrevBtn" onclick="PersonalApp.prevStep()" style="display:none;">
                    <i class="fa-solid fa-arrow-left"></i> Anterior
                </button>
                <div style="display:flex;gap:.5rem;margin-left:auto;">
                    <button type="button" class="btn btn-outline" onclick="PersonalApp.closeModal()">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="wizardNextBtn" onclick="PersonalApp.nextStep()">
                        Siguiente <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <button type="button" class="btn btn-primary" id="wizardSaveBtn" onclick="PersonalApp.save()" style="display:none;">
                        <span id="saveBtnText">Guardar</span>
                        <span id="saveBtnSpinner" class="animales-btn-spinner" style="display:none;"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════ MODAL ÉXITO ═══════════════ --}}
    <div id="successModal" class="admin-modal-overlay" style="display:none;" onclick="if(event.target===this)PersonalApp.closeSuccessModal();">
        <div class="admin-modal" style="max-width:420px;">
            <div class="admin-modal__body" style="text-align:center;padding:2rem;">
                <div style="font-size:3rem;color:var(--accent);margin-bottom:.75rem;"><i class="fa-solid fa-circle-check"></i></div>
                <h3 style="margin:0 0 .5rem;">Usuario creado</h3>
                <p class="muted" style="margin:0 0 1.25rem;">El usuario se ha creado correctamente.</p>
                <div style="display:flex;gap:.5rem;justify-content:center;">
                    <button type="button" class="btn btn-outline" onclick="PersonalApp.closeSuccessModal()">Volver a la lista</button>
                    <button type="button" class="btn btn-primary" onclick="PersonalApp.closeSuccessModal();PersonalApp.openModal();">Crear otro</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    const PersonalApp = (function() {
        const CSRF = '{{ csrf_token() }}';
        const BASE = '{{ route("admin.usuarios.index") }}';
        const MODULOS = ['dashboard', 'articulos', 'personal', 'razas', 'ventas', 'animales', 'auditoria'];

        let currentStep = 1;
        let maxStep = 3;
        let isEditing = false;

        // ─── AJAX ───
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

        // ─── LOAD USERS ───
        function loadUsers() {
            var estado = document.getElementById('filtro-estado-user').value;
            var url = BASE + (estado ? '?estado=' + estado : '');

            document.getElementById('users-loading').style.display = 'flex';

            ajax(url, 'GET').then(function(res) {
                document.getElementById('users-loading').style.display = 'none';
                renderTable(res.usuarios);
            }).catch(function() {
                document.getElementById('users-loading').style.display = 'none';
            });
        }

        function esc(str) {
            if (str == null) return '';
            var div = document.createElement('div');
            div.textContent = String(str);
            return div.innerHTML;
        }

        function renderTable(users) {
            var tbody = document.getElementById('users-tbody');
            var empty = document.getElementById('users-empty');
            tbody.innerHTML = '';

            if (!users || users.length === 0) {
                empty.style.display = 'block';
                document.getElementById('users-table').style.display = 'none';
                return;
            }
            empty.style.display = 'none';
            document.getElementById('users-table').style.display = '';

            users.forEach(function(u) {
                var tr = document.createElement('tr');
                if (!u.activo) tr.style.opacity = '0.55';

                var rolBadge = u.is_super_admin
                    ? '<span class="badge badge--super">Super Admin</span>'
                    : (u.rol_label
                        ? '<span class="badge badge--role">' + esc(u.rol_label) + '</span>'
                        : '<span class="badge badge--custom">Personalizado</span>');

                var estadoBadge = u.activo
                    ? '<span class="pill pill-published">Activo</span>'
                    : '<span class="pill pill-draft">Inactivo</span>';

                var modulos = '';
                if (u.is_super_admin) {
                    modulos = '<span class="badge badge--all">Acceso total</span>';
                } else if (u.rol) {
                    var rolAccess = { supervisor: 'App: Pesos', encargado: 'App: Pesajes', veterinario: 'App: Veterinario' };
                    modulos = '<span class="badge badge--mod">' + esc(rolAccess[u.rol] || u.rol) + '</span>';
                } else if (u.modulos_acceso && u.modulos_acceso.length > 0) {
                    u.modulos_acceso.forEach(function(m) {
                        modulos += '<span class="badge badge--mod">' + esc(m) + '</span>';
                    });
                } else {
                    modulos = '<span class="muted">Sin acceso</span>';
                }

                var toggleLabel = u.activo ? 'Desactivar' : 'Activar';
                var toggleIcon = u.activo ? 'fa-user-slash' : 'fa-user-check';

                tr.innerHTML =
                    '<td><strong>' + esc(u.name) + '</strong></td>' +
                    '<td>' + esc(u.username) + '</td>' +
                    '<td>' + rolBadge + '</td>' +
                    '<td><div class="badges-wrap">' + modulos + '</div></td>' +
                    '<td>' + estadoBadge + '</td>' +
                    '<td>' +
                        '<div class="row" style="gap:.25rem;justify-content:flex-end;">' +
                            '<button class="btn btn-outline btn-small" onclick="PersonalApp.openModal(' + u.id + ')" title="Editar"><i class="fa-solid fa-pen"></i></button>' +
                            '<button class="btn btn-outline btn-small" onclick="PersonalApp.toggleActivo(' + u.id + ')" title="' + toggleLabel + '"><i class="fa-solid ' + toggleIcon + '"></i></button>' +
                        '</div>' +
                    '</td>';
                tbody.appendChild(tr);
            });
        }

        // ─── MODAL ───
        function openModal(id) {
            isEditing = !!id;
            currentStep = 1;
            clearErrors();
            resetForm();

            document.getElementById('userModalTitle').textContent = isEditing ? 'Editar usuario' : 'Agregar usuario';
            document.getElementById('pwdHint').textContent = isEditing ? '' : '*';
            document.getElementById('userId').value = id || '';

            if (isEditing) {
                ajax(BASE + '/' + id, 'GET').then(function(user) {
                    document.getElementById('u-name').value = user.name;
                    document.getElementById('u-username').value = user.username;
                    document.getElementById('u-activo').checked = user.activo;
                    document.getElementById('u-is_super_admin').checked = user.is_super_admin;
                    document.getElementById('u-rol').value = user.rol || '';

                    // Llenar permisos
                    if (user.permisos) {
                        Object.keys(user.permisos).forEach(function(modulo) {
                            var p = user.permisos[modulo];
                            ['puede_ver', 'puede_agregar', 'puede_editar', 'puede_eliminar'].forEach(function(accion) {
                                var cb = document.querySelector('.perm-cb[data-modulo="' + modulo + '"][data-accion="' + accion + '"]');
                                if (cb) cb.checked = !!p[accion];
                            });
                        });
                    }

                    // Llenar campos animales
                    if (user.campos_animales) {
                        Object.keys(user.campos_animales).forEach(function(campo) {
                            var cb = document.querySelector('.campo-cb[data-campo="' + campo + '"]');
                            if (cb) cb.checked = !!user.campos_animales[campo];
                        });
                    }

                    onSuperAdminChange();
                    onRolChange();
                    updatePermDependencies();
                    showStep(1);
                }).catch(function() {
                    alert('Error al cargar datos del usuario.');
                });
            } else {
                onSuperAdminChange();
                showStep(1);
            }

            document.getElementById('userModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('userModal').style.display = 'none';
        }

        function resetForm() {
            document.getElementById('u-name').value = '';
            document.getElementById('u-username').value = '';
            document.getElementById('u-password').value = '';
            document.getElementById('u-password_confirmation').value = '';
            document.getElementById('u-activo').checked = true;
            document.getElementById('u-is_super_admin').checked = false;
            document.getElementById('u-rol').value = '';
            document.getElementById('rolMsg').style.display = 'none';

            document.querySelectorAll('.perm-cb').forEach(function(cb) { cb.checked = false; cb.disabled = false; });
            document.querySelectorAll('.campo-cb').forEach(function(cb) { cb.checked = false; });
        }

        function clearErrors() {
            document.querySelectorAll('#userModal .field-error').forEach(function(e) { e.textContent = ''; });
        }

        // ─── WIZARD NAVIGATION ───
        function showStep(step) {
            currentStep = step;
            document.querySelectorAll('.wizard-panel').forEach(function(p) { p.classList.remove('active'); });
            document.getElementById('step-' + step).classList.add('active');

            document.querySelectorAll('.wizard-step').forEach(function(s) {
                var n = parseInt(s.dataset.step);
                s.classList.toggle('active', n === step);
                s.classList.toggle('completed', n < step);
            });

            var isSuperAdmin = document.getElementById('u-is_super_admin').checked;
            var animalesEditar = getPermCb('animales', 'puede_editar');

            // Determine max step
            if (isSuperAdmin) {
                maxStep = 1;
            } else if (!animalesEditar) {
                maxStep = 2;
            } else {
                maxStep = 3;
            }

            // Wizard step indicators enabled/disabled
            document.querySelectorAll('.wizard-step').forEach(function(s) {
                var n = parseInt(s.dataset.step);
                s.classList.toggle('disabled', n > maxStep);
            });

            // Prev button
            document.getElementById('wizardPrevBtn').style.display = step > 1 ? '' : 'none';

            // Next vs Save
            if (step >= maxStep) {
                document.getElementById('wizardNextBtn').style.display = 'none';
                document.getElementById('wizardSaveBtn').style.display = '';
            } else {
                document.getElementById('wizardNextBtn').style.display = '';
                document.getElementById('wizardSaveBtn').style.display = 'none';
            }
        }

        function nextStep() {
            if (currentStep === 1 && !validateStep1()) return;
            if (currentStep < maxStep) showStep(currentStep + 1);
        }

        function prevStep() {
            if (currentStep > 1) showStep(currentStep - 1);
        }

        function goToStep(step) {
            if (step <= maxStep) {
                if (step > 1 && !validateStep1()) return;
                showStep(step);
            }
        }

        function validateStep1() {
            clearErrors();
            var valid = true;
            var name = document.getElementById('u-name').value.trim();
            var username = document.getElementById('u-username').value.trim();
            var pwd = document.getElementById('u-password').value;
            var pwd2 = document.getElementById('u-password_confirmation').value;

            if (!name) {
                document.getElementById('err-name').textContent = 'El nombre es requerido.';
                valid = false;
            }
            if (!username) {
                document.getElementById('err-username').textContent = 'El nombre de usuario es requerido.';
                valid = false;
            } else if (!/^[a-zA-Z0-9._-]+$/.test(username)) {
                document.getElementById('err-username').textContent = 'Solo letras, números, puntos, guiones y guiones bajos.';
                valid = false;
            }
            if (!isEditing && !pwd) {
                document.getElementById('err-password').textContent = 'La contraseña es requerida.';
                valid = false;
            }
            if (pwd && pwd.length < 6) {
                document.getElementById('err-password').textContent = 'Mínimo 6 caracteres.';
                valid = false;
            }
            if (pwd && pwd !== pwd2) {
                document.getElementById('err-password').textContent = 'Las contraseñas no coinciden.';
                valid = false;
            }
            return valid;
        }

        // ─── SUPER ADMIN TOGGLE ───
        function onSuperAdminChange() {
            var isSuperAdmin = document.getElementById('u-is_super_admin').checked;
            document.getElementById('superAdminMsg').style.display = isSuperAdmin ? 'flex' : 'none';

            if (isSuperAdmin) {
                document.getElementById('u-rol').value = '';
                document.getElementById('u-rol').disabled = true;
                document.getElementById('rolMsg').style.display = 'none';
            } else {
                document.getElementById('u-rol').disabled = false;
            }

            recalcMaxStep();
        }

        // ─── ROL CHANGE ───
        function onRolChange() {
            var rol = document.getElementById('u-rol').value;
            var msg = document.getElementById('rolMsg');
            var msgText = document.getElementById('rolMsgText');

            if (rol) {
                var texts = {
                    encargado: 'El encargado solo tendrá acceso a App: Pesajes (pesaje de leche + registro de nacimientos). No necesita permisos específicos.',
                    veterinario: 'El veterinario solo tendrá acceso a App: Veterinario. No necesita permisos específicos.',
                    supervisor: 'El supervisor solo tendrá acceso a App: Pesos (registro de peso diario). No necesita permisos específicos.',
                };
                msgText.textContent = texts[rol] || '';
                msg.style.display = 'flex';
            } else {
                msg.style.display = 'none';
            }

            recalcMaxStep();
        }

        function recalcMaxStep() {
            var isSuperAdmin = document.getElementById('u-is_super_admin').checked;
            var rol = document.getElementById('u-rol').value;
            var animalesEditar = getPermCb('animales', 'puede_editar');

            document.querySelectorAll('.wizard-step').forEach(function(s) {
                var n = parseInt(s.dataset.step);
                if (n > 1) s.classList.toggle('disabled', isSuperAdmin || !!rol);
            });

            if (isSuperAdmin || rol) {
                maxStep = 1;
                if (currentStep > 1) showStep(1);
                document.getElementById('wizardNextBtn').style.display = 'none';
                document.getElementById('wizardSaveBtn').style.display = '';
            } else {
                maxStep = animalesEditar ? 3 : 2;
                if (currentStep <= 1) {
                    document.getElementById('wizardNextBtn').style.display = '';
                    document.getElementById('wizardSaveBtn').style.display = 'none';
                }
            }
        }

        // ─── PERMISSION LOGIC ───
        function getPermCb(modulo, accion) {
            var cb = document.querySelector('.perm-cb[data-modulo="' + modulo + '"][data-accion="' + accion + '"]');
            return cb ? cb.checked : false;
        }

        function onPermChange(el) {
            var modulo = el.dataset.modulo;
            var accion = el.dataset.accion;

            if (accion === 'puede_ver' && !el.checked) {
                // Desmarcar ver → desmarcar y deshabilitar agregar, editar, eliminar
                ['puede_agregar', 'puede_editar', 'puede_eliminar'].forEach(function(a) {
                    var cb = document.querySelector('.perm-cb[data-modulo="' + modulo + '"][data-accion="' + a + '"]');
                    if (cb) { cb.checked = false; cb.disabled = true; }
                });
            } else if (accion === 'puede_ver' && el.checked) {
                ['puede_agregar', 'puede_editar', 'puede_eliminar'].forEach(function(a) {
                    var cb = document.querySelector('.perm-cb[data-modulo="' + modulo + '"][data-accion="' + a + '"]');
                    if (cb) cb.disabled = false;
                });
            }

            if (['puede_agregar', 'puede_editar', 'puede_eliminar'].indexOf(accion) !== -1 && el.checked) {
                // Auto-marcar ver
                var verCb = document.querySelector('.perm-cb[data-modulo="' + modulo + '"][data-accion="puede_ver"]');
                if (verCb && !verCb.checked) verCb.checked = true;
            }

            updatePermDependencies();
        }

        function updatePermDependencies() {
            // Disable sub-perms when ver is unchecked
            MODULOS.forEach(function(modulo) {
                if (modulo === 'dashboard') return;
                var verCb = document.querySelector('.perm-cb[data-modulo="' + modulo + '"][data-accion="puede_ver"]');
                if (!verCb) return;
                if (!verCb.checked) {
                    ['puede_agregar', 'puede_editar', 'puede_eliminar'].forEach(function(a) {
                        var cb = document.querySelector('.perm-cb[data-modulo="' + modulo + '"][data-accion="' + a + '"]');
                        if (cb) { cb.checked = false; cb.disabled = true; }
                    });
                } else {
                    ['puede_agregar', 'puede_editar', 'puede_eliminar'].forEach(function(a) {
                        var cb = document.querySelector('.perm-cb[data-modulo="' + modulo + '"][data-accion="' + a + '"]');
                        if (cb) cb.disabled = false;
                    });
                }
            });

            // Animales editar → show/hide step 3 hint and update maxStep
            var animalesEditar = getPermCb('animales', 'puede_editar');
            document.getElementById('configCamposHint').style.display = animalesEditar ? '' : 'none';

            if (!document.getElementById('u-is_super_admin').checked && !document.getElementById('u-rol').value) {
                maxStep = animalesEditar ? 3 : 2;
                showStep(currentStep > maxStep ? maxStep : currentStep);
            }
        }

        // ─── CAMPOS SECTIONS ───
        function toggleSection(btn, state) {
            var section = btn.closest('.campos-section');
            section.querySelectorAll('.campo-cb').forEach(function(cb) { cb.checked = state; });
        }

        // ─── SAVE ───
        function save() {
            if (!validateStep1()) { showStep(1); return; }

            var id = document.getElementById('userId').value;
            var isSuperAdmin = document.getElementById('u-is_super_admin').checked;
            var rol = document.getElementById('u-rol').value;

            var data = {
                name: document.getElementById('u-name').value.trim(),
                username: document.getElementById('u-username').value.trim(),
                is_super_admin: isSuperAdmin,
                activo: document.getElementById('u-activo').checked,
                rol: rol || null,
            };

            var pwd = document.getElementById('u-password').value;
            if (pwd) {
                data.password = pwd;
                data.password_confirmation = document.getElementById('u-password_confirmation').value;
            }

            if (!isSuperAdmin && !rol) {
                data.permisos = {};
                MODULOS.forEach(function(modulo) {
                    data.permisos[modulo] = {};
                    ['puede_ver', 'puede_agregar', 'puede_editar', 'puede_eliminar'].forEach(function(accion) {
                        var cb = document.querySelector('.perm-cb[data-modulo="' + modulo + '"][data-accion="' + accion + '"]');
                        data.permisos[modulo][accion] = cb ? cb.checked : false;
                    });
                });

                data.campos_animales = {};
                document.querySelectorAll('.campo-cb').forEach(function(cb) {
                    data.campos_animales[cb.dataset.campo] = cb.checked;
                });
            }

            document.getElementById('saveBtnText').style.display = 'none';
            document.getElementById('saveBtnSpinner').style.display = 'inline-block';
            document.getElementById('wizardSaveBtn').disabled = true;

            var url = id ? BASE + '/' + id : BASE;
            var method = id ? 'PUT' : 'POST';

            ajax(url, method, data).then(function(res) {
                closeModal();
                loadUsers();
                if (!id) {
                    document.getElementById('successModal').style.display = 'flex';
                }
                if (id) {
                    // Advertencia de sesión si se editan permisos
                    if (!isSuperAdmin) {
                        showToast('Cambios guardados. Los permisos aplicarán en el próximo inicio de sesión del usuario.');
                    }
                }
            }).catch(function(err) {
                if (err.errors) {
                    clearErrors();
                    Object.keys(err.errors).forEach(function(field) {
                        var el = document.getElementById('err-' + field);
                        if (el) el.textContent = err.errors[field][0];
                    });
                    if (err.errors.name || err.errors.username || err.errors.password) showStep(1);
                } else {
                    alert('Error: ' + (err.message || 'Error desconocido'));
                }
            }).finally(function() {
                document.getElementById('saveBtnText').style.display = '';
                document.getElementById('saveBtnSpinner').style.display = 'none';
                document.getElementById('wizardSaveBtn').disabled = false;
            });
        }

        // ─── TOGGLE ACTIVO ───
        function toggleActivo(id) {
            if (!confirm('¿Cambiar el estado de este usuario?')) return;
            ajax(BASE + '/' + id + '/desactivar', 'PUT').then(function(res) {
                loadUsers();
                showToast(res.message);
            }).catch(function(err) {
                alert(err.message || 'Error al cambiar estado.');
            });
        }

        // ─── SUCCESS MODAL ───
        function closeSuccessModal() {
            document.getElementById('successModal').style.display = 'none';
        }

        // ─── TOAST ───
        function showToast(msg) {
            var toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.textContent = msg;
            document.body.appendChild(toast);
            setTimeout(function() { toast.classList.add('show'); }, 10);
            setTimeout(function() {
                toast.classList.remove('show');
                setTimeout(function() { toast.remove(); }, 300);
            }, 4000);
        }

        // ─── INIT ───
        document.addEventListener('DOMContentLoaded', loadUsers);

        return {
            loadUsers: loadUsers,
            openModal: openModal,
            closeModal: closeModal,
            nextStep: nextStep,
            prevStep: prevStep,
            goToStep: goToStep,
            onSuperAdminChange: onSuperAdminChange,
            onRolChange: onRolChange,
            onPermChange: onPermChange,
            toggleSection: toggleSection,
            save: save,
            toggleActivo: toggleActivo,
            closeSuccessModal: closeSuccessModal,
        };
    })();
    </script>
@endsection
