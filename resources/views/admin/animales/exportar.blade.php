@extends('layouts.admin')

@section('title', 'Exportar animales')

@section('actions')
    <div class="row" style="gap:.5rem;">
        <a href="{{ route('admin.animales.index') }}" class="btn btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
        <button class="btn btn-primary" onclick="ExportApp.exportAll()">
            <i class="fa-solid fa-file-excel"></i> Exportar todos
        </button>
        <button class="btn btn-primary" id="btn-export-sel" onclick="ExportApp.exportSelected()" disabled>
            <i class="fa-solid fa-file-excel"></i> Exportar seleccionados (<span id="sel-count">0</span>)
        </button>
    </div>
@endsection

@section('content')
    {{-- ═══════════════ FILTROS ═══════════════ --}}
    <div class="animales-filters">
        <div class="animales-filters__grid">
            <div>
                <label for="exp-agropecuaria">Agropecuaria</label>
                <select id="exp-agropecuaria" onchange="ExportApp.applyFilters()">
                    <option value="">Todas</option>
                </select>
            </div>
            <div>
                <label for="exp-estado">Estado</label>
                <select id="exp-estado" onchange="ExportApp.applyFilters()">
                    <option value="">Todos</option>
                </select>
            </div>
            <div>
                <label for="exp-reproductivo">Estado reproductivo</label>
                <select id="exp-reproductivo" onchange="ExportApp.applyFilters()">
                    <option value="">Todos</option>
                </select>
            </div>
            <div>
                <label for="exp-sexo">Sexo</label>
                <select id="exp-sexo" onchange="ExportApp.applyFilters()">
                    <option value="">Todos</option>
                </select>
            </div>
            <div class="animales-filters__search">
                <label for="exp-busqueda">Búsqueda</label>
                <input id="exp-busqueda" type="text" placeholder="Nombre, código o ID electrónico..."
                       onkeydown="if(event.key==='Enter'){event.preventDefault();ExportApp.applyFilters();}">
            </div>
        </div>
        <div class="animales-filters__actions">
            <button class="btn btn-outline btn-small" onclick="ExportApp.clearFilters()">
                <i class="fa-solid fa-xmark"></i> Limpiar filtros
            </button>
            <button class="btn btn-primary btn-small" onclick="ExportApp.applyFilters()">
                <i class="fa-solid fa-magnifying-glass"></i> Buscar
            </button>
            <span id="exp-total-badge" class="pill pill-published" style="display:none;"></span>
        </div>
    </div>

    {{-- ═══════════════ TABLA ═══════════════ --}}
    <div id="exp-loading" class="animales-loading" style="display:none;">
        <div class="animales-spinner"></div>
        <span>Cargando datos...</span>
    </div>

    <div id="exp-table-wrap">
        <div class="exp-dual-grid" id="exp-dual-grid"></div>
    </div>

    {{-- ═══════════════ PAGINACIÓN ═══════════════ --}}
    <div id="exp-pagination" style="display:flex;justify-content:center;gap:.25rem;flex-wrap:wrap;margin-top:1rem;"></div>

    {{-- ═══════════════ MODAL PROGRESO ═══════════════ --}}
    <div id="export-modal-overlay" class="import-modal-overlay" style="display:none;">
        <div class="import-modal">
            <div id="export-modal-processing">
                <h3 class="import-modal__title"><i class="fa-solid fa-file-excel"></i> Generando archivo</h3>
                <div class="animales-progress-bar">
                    <div class="animales-progress-fill" id="export-progress-fill" style="width:0%;"></div>
                </div>
                <p class="muted" id="export-progress-text" style="margin:.5rem 0 0;text-align:center;">Obteniendo datos...</p>
            </div>
            <div id="export-modal-done" style="display:none;">
                <h3 class="import-modal__title"><i class="fa-solid fa-circle-check" style="color:var(--accent);"></i> Exportación completada</h3>
                <p style="margin:0 0 .75rem;font-size:.88rem;">El archivo se descargó correctamente.</p>
                <div style="text-align:right;">
                    <button type="button" class="btn btn-primary" onclick="ExportApp.closeModal()">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
<script>
var ExportApp = (function () {
    'use strict';

    var BASE_URL    = '{{ route("admin.animales.index") }}';
    var FILTERS_URL = '{{ route("admin.animales.filtros") }}';
    var EXPORT_URL  = '{{ route("admin.animales.exportar") }}';
    var CSRF        = '{{ csrf_token() }}';

    var EXCEL_HEADERS = [
        { header: 'Agropecuaria',                                      field: 'agropecuaria' },
        { header: 'Código práctico',                                    field: 'codigo_practico' },
        { header: 'Estado',                                             field: 'estado' },
        { header: 'Identificación electrónica',                         field: 'identificacion_electronica' },
        { header: 'Fecha de nacimiento',                                field: 'fecha_nacimiento', date: true },
        { header: 'Padre: nombre del animal',                           field: 'padre_nombre' },
        { header: 'Código de la madre',                                 field: 'codigo_madre' },
        { header: 'Última locación',                                    field: 'ultima_locacion' },
        { header: 'Composición racial',                                 field: 'composicion_racial' },
        { header: 'Clasificación asociación',                           field: 'clasificacion_asociacion' },
        { header: 'Último peso',                                        field: 'ultimo_peso' },
        { header: 'Estandarización de producción',                      field: 'estandarizacion_produccion' },
        { header: 'Fecha del último servicio',                          field: 'fecha_ultimo_servicio', date: true },
        { header: 'Estado actual reproductivo',                         field: 'estado_reproductivo' },
        { header: 'Número de revisiones',                               field: 'numero_revisiones' },
        { header: 'Fecha del último parto/aborto',                      field: 'fecha_ultimo_parto', date: true },
        { header: 'Fecha de secado',                                    field: 'fecha_secado', date: true },
        { header: 'Nombre',                                             field: 'nombre' },
        { header: 'Sexo',                                               field: 'sexo' },
        { header: 'Código único del reproductor último servicio',       field: 'codigo_reproductor' },
        { header: 'Codigo',                                             field: 'codigo' },
        { header: 'Codigo nombre',                                      field: 'codigo_nombre' },
    ];

    var PER_PAGE = 50;
    var currentPage = 1;
    var lastPage = 1;
    var totalRecords = 0;
    var pageData = [];
    var selectedIds = {};

    // ─── AJAX helper ───
    function ajax(url) {
        return fetch(url, {
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        }).then(function (r) { return r.json(); });
    }

    // ─── Filters ───
    function getFilterParams() {
        var params = new URLSearchParams();
        var v;
        v = document.getElementById('exp-agropecuaria').value;    if (v) params.set('agropecuaria', v);
        v = document.getElementById('exp-estado').value;          if (v) params.set('estado', v);
        v = document.getElementById('exp-reproductivo').value;    if (v) params.set('estado_reproductivo', v);
        v = document.getElementById('exp-sexo').value;            if (v) params.set('sexo', v);
        v = document.getElementById('exp-busqueda').value.trim(); if (v) params.set('busqueda', v);
        return params;
    }

    function loadFilterOptions() {
        ajax(FILTERS_URL).then(function (data) {
            fillSelect('exp-agropecuaria', data.agropecuarias);
            fillSelect('exp-estado', data.estados);
            fillSelect('exp-reproductivo', data.estados_reproductivos);
            fillSelect('exp-sexo', data.sexos);
            loadData();
        });
    }

    function fillSelect(id, items) {
        var sel = document.getElementById(id);
        var first = sel.options[0].outerHTML;
        sel.innerHTML = first;
        (items || []).forEach(function (v) {
            var opt = document.createElement('option');
            opt.value = v; opt.textContent = v;
            sel.appendChild(opt);
        });
    }

    function applyFilters() {
        currentPage = 1;
        loadData();
    }

    function clearFilters() {
        document.getElementById('exp-agropecuaria').value = '';
        document.getElementById('exp-estado').value = '';
        document.getElementById('exp-reproductivo').value = '';
        document.getElementById('exp-sexo').value = '';
        document.getElementById('exp-busqueda').value = '';
        currentPage = 1;
        loadData();
    }

    // ─── Load Data (paginated, reuses index endpoint) ───
    function loadData() {
        document.getElementById('exp-loading').style.display = 'flex';
        document.getElementById('exp-dual-grid').innerHTML = '';
        document.getElementById('exp-pagination').innerHTML = '';

        var params = getFilterParams();
        params.set('page', currentPage);
        params.set('per_page', PER_PAGE);
        params.set('sort', 'codigo_practico');
        params.set('dir', 'asc');

        ajax(BASE_URL + '?' + params.toString()).then(function (resp) {
            pageData = resp.data || [];
            currentPage = resp.current_page || 1;
            lastPage = resp.last_page || 1;
            totalRecords = resp.total || 0;

            document.getElementById('exp-loading').style.display = 'none';

            var badge = document.getElementById('exp-total-badge');
            badge.style.display = totalRecords > 0 ? '' : 'none';
            badge.textContent = totalRecords.toLocaleString('es-CR') + ' registros';

            renderDualTable();
            renderPagination();
        }).catch(function () {
            document.getElementById('exp-loading').style.display = 'none';
        });
    }

    function goToPage(p) {
        if (p < 1 || p > lastPage) return;
        currentPage = p;
        loadData();
    }

    // ─── Render dual-column table ───
    function renderDualTable() {
        var grid = document.getElementById('exp-dual-grid');
        if (!pageData.length) {
            grid.innerHTML = '<p class="muted" style="grid-column:1/-1;text-align:center;padding:2rem 0;">No se encontraron registros.</p>';
            return;
        }

        var mid = Math.ceil(pageData.length / 2);
        var left = pageData.slice(0, mid);
        var right = pageData.slice(mid);

        grid.innerHTML = buildTable(left) + buildTable(right);
        restoreChecks();
    }

    function buildTable(items) {
        if (!items.length) return '<div></div>';
        var html = '<table class="exp-table"><thead><tr>'
            + '<th class="exp-table__check"><input type="checkbox" onchange="ExportApp.togglePageAll(this)"></th>'
            + '<th>Cód. práctico</th>'
            + '<th>ID electrónica</th>'
            + '<th>Nombre</th>'
            + '</tr></thead><tbody>';
        items.forEach(function (a) {
            var checked = selectedIds[a.id] ? ' checked' : '';
            html += '<tr>'
                + '<td class="exp-table__check"><input type="checkbox" value="' + a.id + '"' + checked + ' onchange="ExportApp.toggleOne(this)"></td>'
                + '<td>' + esc(a.codigo_practico) + '</td>'
                + '<td>' + esc(a.identificacion_electronica) + '</td>'
                + '<td>' + esc(a.nombre) + '</td>'
                + '</tr>';
        });
        html += '</tbody></table>';
        return html;
    }

    function esc(v) { return v == null ? '' : String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    function restoreChecks() {
        document.querySelectorAll('#exp-dual-grid input[type=checkbox][value]').forEach(function (cb) {
            cb.checked = !!selectedIds[parseInt(cb.value)];
        });
        updateHeaderChecks();
    }

    // ─── Selection ───
    function toggleOne(cb) {
        var id = parseInt(cb.value);
        if (cb.checked) { selectedIds[id] = true; }
        else { delete selectedIds[id]; }
        updateSelBadge();
        updateHeaderChecks();
    }

    function togglePageAll(headerCb) {
        var table = headerCb.closest('table');
        var checks = table.querySelectorAll('tbody input[type=checkbox]');
        checks.forEach(function (cb) {
            cb.checked = headerCb.checked;
            var id = parseInt(cb.value);
            if (headerCb.checked) { selectedIds[id] = true; }
            else { delete selectedIds[id]; }
        });
        updateSelBadge();
    }

    function updateHeaderChecks() {
        document.querySelectorAll('#exp-dual-grid thead input[type=checkbox]').forEach(function (hcb) {
            var table = hcb.closest('table');
            var checks = table.querySelectorAll('tbody input[type=checkbox]');
            if (!checks.length) { hcb.checked = false; return; }
            var allChecked = true;
            checks.forEach(function (cb) { if (!cb.checked) allChecked = false; });
            hcb.checked = allChecked;
        });
    }

    function updateSelBadge() {
        var n = Object.keys(selectedIds).length;
        document.getElementById('sel-count').textContent = n;
        document.getElementById('btn-export-sel').disabled = (n === 0);
    }

    // ─── Pagination ───
    function renderPagination() {
        var wrap = document.getElementById('exp-pagination');
        if (lastPage <= 1) { wrap.innerHTML = ''; return; }
        var html = '';
        html += '<button class="animales-page' + (currentPage <= 1 ? ' disabled' : '') + '" onclick="ExportApp.goToPage(' + (currentPage - 1) + ')">&laquo;</button>';
        var pages = getPaginationPages(currentPage, lastPage);
        pages.forEach(function (p) {
            if (p === '...') {
                html += '<span class="animales-page disabled">…</span>';
            } else {
                html += '<button class="animales-page' + (p === currentPage ? ' active' : '') + '" onclick="ExportApp.goToPage(' + p + ')">' + p + '</button>';
            }
        });
        html += '<button class="animales-page' + (currentPage >= lastPage ? ' disabled' : '') + '" onclick="ExportApp.goToPage(' + (currentPage + 1) + ')">&raquo;</button>';
        wrap.innerHTML = html;
    }

    function getPaginationPages(cur, last) {
        if (last <= 7) {
            var arr = []; for (var i=1;i<=last;i++) arr.push(i); return arr;
        }
        var pages = [1];
        if (cur > 3) pages.push('...');
        for (var j = Math.max(2, cur-1); j <= Math.min(last-1, cur+1); j++) pages.push(j);
        if (cur < last-2) pages.push('...');
        pages.push(last);
        return pages;
    }

    // ─── Date formatting ───
    function formatDate(val) {
        if (!val) return '';
        var d = new Date(val);
        if (isNaN(d.getTime())) return val;
        return String(d.getDate()).padStart(2,'0') + '/' + String(d.getMonth()+1).padStart(2,'0') + '/' + d.getFullYear();
    }

    // ─── EXPORT ───
    function exportAll() {
        doExport(false);
    }

    function exportSelected() {
        var ids = Object.keys(selectedIds);
        if (!ids.length) return;
        doExport(true);
    }

    function doExport(onlySelected) {
        var overlay = document.getElementById('export-modal-overlay');
        var processing = document.getElementById('export-modal-processing');
        var done = document.getElementById('export-modal-done');
        var fill = document.getElementById('export-progress-fill');
        var text = document.getElementById('export-progress-text');

        overlay.style.display = 'flex';
        processing.style.display = '';
        done.style.display = 'none';
        fill.style.width = '20%';
        text.textContent = 'Obteniendo datos...';

        var params;
        if (onlySelected) {
            params = new URLSearchParams();
            params.set('ids', Object.keys(selectedIds).join(','));
        } else {
            params = getFilterParams();
        }

        ajax(EXPORT_URL + '?' + params.toString()).then(function (data) {
            fill.style.width = '60%';
            text.textContent = 'Generando Excel (' + data.length + ' registros)...';

            setTimeout(function () {
                generateXlsx(data);
                fill.style.width = '100%';
                text.textContent = '¡Listo!';
                setTimeout(function () {
                    processing.style.display = 'none';
                    done.style.display = '';
                }, 400);
            }, 100);
        }).catch(function (err) {
            overlay.style.display = 'none';
            alert('Error al exportar: ' + (err.message || err));
        });
    }

    function generateXlsx(data) {
        var rows = data.map(function (animal) {
            var row = {};
            EXCEL_HEADERS.forEach(function (col) {
                var val = animal[col.field];
                if (col.date) { row[col.header] = formatDate(val); }
                else if (val === null || val === undefined) { row[col.header] = ''; }
                else { row[col.header] = val; }
            });
            return row;
        });

        var ws = XLSX.utils.json_to_sheet(rows, { header: EXCEL_HEADERS.map(function (c) { return c.header; }) });

        var colWidths = EXCEL_HEADERS.map(function (col) {
            var max = col.header.length;
            rows.forEach(function (r) {
                var v = String(r[col.header] || '');
                if (v.length > max) max = v.length;
            });
            return { wch: Math.min(max + 2, 40) };
        });
        ws['!cols'] = colWidths;

        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Animales');

        var today = new Date();
        var dateStr = today.getFullYear() + '-' + String(today.getMonth()+1).padStart(2,'0') + '-' + String(today.getDate()).padStart(2,'0');
        XLSX.writeFile(wb, 'animales_' + dateStr + '.xlsx');
    }

    // ─── Modal ───
    function closeModal() {
        document.getElementById('export-modal-overlay').style.display = 'none';
    }

    // ─── Init ───
    document.addEventListener('DOMContentLoaded', loadFilterOptions);

    return {
        applyFilters: applyFilters,
        clearFilters: clearFilters,
        goToPage: goToPage,
        toggleOne: toggleOne,
        togglePageAll: togglePageAll,
        exportAll: exportAll,
        exportSelected: exportSelected,
        closeModal: closeModal
    };
})();
</script>
@endpush
