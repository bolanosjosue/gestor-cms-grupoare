@extends('layouts.admin')

@section('title', 'Importar animales')

@section('actions')
    <div class="row" style="gap:.5rem;">
        <a href="{{ route('admin.animales.index') }}" class="btn btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
        <button class="btn btn-primary" id="import-trigger-btn" onclick="AnimalesImportApp.triggerFileInput()">
            <i class="fa-solid fa-file-import"></i> Importar archivo
        </button>
        <input id="import-file-input" type="file" accept=".xlsx,.xls" style="display:none;" onchange="AnimalesImportApp.handleFileSelect(event)">
    </div>
@endsection

@section('content')
    <div class="import-page">
        {{-- Modal de progreso / resultado --}}
        <div id="import-modal-overlay" class="import-modal-overlay" style="display:none;">
            <div class="import-modal">
                <div id="import-modal-processing">
                    <h3 class="import-modal__title"><i class="fa-solid fa-file-import"></i> Importando archivo</h3>
                    <p class="muted" id="import-status-filename" style="margin:0 0 .75rem;font-size:.85rem;"></p>
                    <div class="animales-progress-bar">
                        <div class="animales-progress-fill" id="import-progress-fill" style="width:0%;"></div>
                    </div>
                    <p class="muted" id="import-progress-text" style="margin:.5rem 0 0;text-align:center;">0%</p>
                </div>
                <div id="import-modal-done" style="display:none;">
                    <h3 class="import-modal__title"><i class="fa-solid fa-circle-check" style="color:var(--accent);"></i> Importación completada</h3>
                    <div class="import-modal__results">
                        <div class="import-stat-chip import-stat-chip--ok"><i class="fa-solid fa-plus"></i> <strong id="res-insertados">0</strong> insertados</div>
                        <div class="import-stat-chip import-stat-chip--upd"><i class="fa-solid fa-pen"></i> <strong id="res-actualizados">0</strong> actualizados</div>
                        <div class="import-stat-chip import-stat-chip--err"><i class="fa-solid fa-triangle-exclamation"></i> <strong id="res-errores">0</strong> errores</div>
                    </div>
                    <div id="import-errors-detail" style="display:none;margin-top:1rem;">
                        <p style="margin:0 0 .5rem;font-size:.85rem;"><strong>Filas con error:</strong></p>
                        <div class="import-errors-scroll">
                            <table class="animales-import-preview-table">
                                <thead><tr><th>Fila</th><th>Identificación</th><th>Error</th></tr></thead>
                                <tbody id="import-errors-tbody"></tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-outline btn-small" style="margin-top:.5rem;" onclick="AnimalesImportApp.downloadErrors()">
                            <i class="fa-solid fa-download"></i> Descargar errores
                        </button>
                    </div>
                    <div style="margin-top:1rem;text-align:right;">
                        <button type="button" class="btn btn-primary" onclick="AnimalesImportApp.closeModal()">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Historial --}}
        <div class="card import-historial-card">
            <h2 class="import-historial-card__title">
                <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i>
                Historial de importaciones
            </h2>
            <p class="muted import-historial-card__lead">Registro de cada importación desde Excel: fecha, archivo y resultados.</p>

            <div id="historial-content">
                @if($historial->isEmpty())
                    <p class="muted" style="margin:0;">Todavía no hay importaciones registradas.</p>
                @else
                    <div class="table-wrap">
                        <table class="import-historial-table">
                            <thead>
                                <tr>
                                    <th>Fecha y hora</th>
                                    <th>Usuario</th>
                                    <th>Archivo</th>
                                    <th class="num">Registros</th>
                                    <th class="num">Insertados</th>
                                    <th class="num">Actualizados</th>
                                    <th class="num">Errores</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($historial as $imp)
                                    <tr>
                                        <td>
                                            <span class="import-historial-fecha">{{ $imp->created_at->timezone(config('app.timezone'))->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</span>
                                            <span class="import-historial-hora">{{ $imp->created_at->format('H:i') }} h</span>
                                        </td>
                                        <td>{{ $imp->user?->name ?? '—' }}</td>
                                        <td class="import-historial-archivo">{{ $imp->nombre_archivo ?? '—' }}</td>
                                        <td class="num">{{ number_format($imp->total_registros) }}</td>
                                        <td class="num import-historial-ok">{{ number_format($imp->insertados) }}</td>
                                        <td class="num import-historial-upd">{{ number_format($imp->actualizados) }}</td>
                                        <td class="num {{ $imp->con_error > 0 ? 'import-historial-err' : '' }}">{{ number_format($imp->con_error) }}</td>
                                        <td>
                                            @if($imp->finalized_at)
                                                <span class="pill pill-published import-historial-pill">Completada</span>
                                            @else
                                                <span class="pill pill-draft import-historial-pill">Incompleta</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="import-historial-pagination">
                        {{ $historial->links('pagination::default') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
    <script>
    const AnimalesImportApp = (function() {
        const CSRF = '{{ csrf_token() }}';
        const IMPORT_URL = @json(route('admin.animales.importar'));
        const HISTORIAL_URL = @json(route('admin.animales.importacion.historial'));
        const BATCH_SIZE = 200;

        let importData = [];
        let importErrors = [];
        let importFileName = '';
        let sessionUuid = '';

        const EXCEL_MAP = {
            'agropecuaria': 'agropecuaria',
            'código práctico': 'codigo_practico',
            'estado': 'estado',
            'identificación electrónica': 'identificacion_electronica',
            'fecha de nacimiento': 'fecha_nacimiento',
            'padre: nombre del animal': 'padre_nombre',
            'código de la madre': 'codigo_madre',
            'última locación': 'ultima_locacion',
            'composición racial': 'composicion_racial',
            'clasificación asociación': 'clasificacion_asociacion',
            'último peso': 'ultimo_peso',
            'estandarización de producción': 'estandarizacion_produccion',
            'fecha del último servicio': 'fecha_ultimo_servicio',
            'estado actual reproductivo': 'estado_reproductivo',
            'número de revisiones': 'numero_revisiones',
            'fecha del último parto/aborto': 'fecha_ultimo_parto',
            'fecha de secado': 'fecha_secado',
            'nombre': 'nombre',
            'sexo': 'sexo',
            'código único del reproductor último servicio': 'codigo_reproductor',
            'codigo': 'codigo',
            'codigo nombre': 'codigo_nombre',
        };

        const REQUIRED_EXCEL_COLS = Object.keys(EXCEL_MAP);

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

        function esc(str) {
            if (str == null) return '';
            var div = document.createElement('div');
            div.textContent = String(str);
            return div.innerHTML;
        }

        function triggerFileInput() {
            document.getElementById('import-file-input').value = '';
            document.getElementById('import-file-input').click();
        }

        function handleFileSelect(e) {
            var file = e.target.files[0];
            importFileName = file ? file.name : '';
            sessionUuid = '';

            if (!file) return;

            var ext = file.name.split('.').pop().toLowerCase();
            if (ext !== 'xlsx' && ext !== 'xls') {
                alert('Solo se aceptan archivos .xlsx o .xls');
                e.target.value = '';
                return;
            }

            if (file.size > 10 * 1024 * 1024) {
                alert('El archivo excede el límite de 10 MB.');
                e.target.value = '';
                return;
            }

            var validMimes = [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel',
                'application/octet-stream',
                ''
            ];
            if (file.type && validMimes.indexOf(file.type) === -1) {
                alert('Tipo de archivo no válido. Solo .xlsx o .xls.');
                e.target.value = '';
                return;
            }

            var reader = new FileReader();
            reader.onload = function(evt) {
                try {
                    var data = new Uint8Array(evt.target.result);
                    var workbook = XLSX.read(data, { type: 'array', cellDates: false });
                    var sheet = workbook.Sheets[workbook.SheetNames[0]];
                    var json = XLSX.utils.sheet_to_json(sheet, { defval: null });

                    if (json.length === 0) {
                        alert('El archivo está vacío.');
                        return;
                    }

                    var headers = Object.keys(json[0]).map(function(h) { return h.toLowerCase().trim(); });
                    var missingCols = [];
                    REQUIRED_EXCEL_COLS.forEach(function(col) {
                        var found = headers.some(function(h) { return h === col; });
                        if (!found) missingCols.push(col);
                    });

                    if (missingCols.length > 0) {
                        alert('Faltan columnas requeridas: ' + missingCols.join(', '));
                        return;
                    }

                    importData = json.filter(function(row) {
                        return Object.values(row).some(function(val) {
                            return val != null && String(val).trim() !== '' && String(val).trim().toLowerCase() !== 'nan';
                        });
                    }).map(function(row) {
                        var mapped = {};
                        var normalizedRow = {};
                        Object.keys(row).forEach(function(k) {
                            normalizedRow[k.toLowerCase().trim()] = row[k];
                        });

                        Object.keys(EXCEL_MAP).forEach(function(excelCol) {
                            var bdField = EXCEL_MAP[excelCol];
                            var val = normalizedRow[excelCol];

                            if (['fecha_nacimiento','fecha_ultimo_servicio','fecha_ultimo_parto','fecha_secado'].indexOf(bdField) !== -1) {
                                mapped[bdField] = parseExcelDate(val);
                            } else if (bdField === 'ultimo_peso') {
                                var n = parseFloat(val);
                                mapped[bdField] = isNaN(n) ? null : n;
                            } else if (bdField === 'numero_revisiones') {
                                var i = parseInt(val, 10);
                                mapped[bdField] = isNaN(i) ? null : i;
                            } else {
                                mapped[bdField] = (val != null && String(val).trim() !== '' && String(val).trim().toLowerCase() !== 'nan')
                                    ? String(val).trim()
                                    : null;
                            }
                        });

                        return mapped;
                    });

                    if (importData.length === 0) {
                        alert('No se encontraron registros válidos.');
                        return;
                    }

                    startImport();
                } catch (ex) {
                    alert('Error al leer el archivo: ' + ex.message);
                }
            };
            reader.readAsArrayBuffer(file);
        }

        function parseExcelDate(val) {
            if (val == null || val === '' || String(val).trim().toLowerCase() === 'nan') return null;
            var s = String(val).trim();
            if (/^\d+(\.\d+)?$/.test(s) && parseFloat(s) > 30000) {
                var serial = parseFloat(s);
                var utcDays = Math.floor(serial - 25569);
                var d = new Date(utcDays * 86400 * 1000);
                if (!isNaN(d.getTime())) {
                    return d.toISOString().split('T')[0];
                }
            }
            var match = s.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})/);
            if (match) {
                return match[3] + '-' + match[2].padStart(2, '0') + '-' + match[1].padStart(2, '0');
            }
            if (/^\d{4}-\d{2}-\d{2}/.test(s)) {
                return s.substring(0, 10);
            }
            return null;
        }

        function startImport() {
            if (importData.length === 0) return;

            document.getElementById('import-modal-overlay').style.display = '';
            document.getElementById('import-modal-processing').style.display = '';
            document.getElementById('import-modal-done').style.display = 'none';
            document.getElementById('import-status-filename').textContent = importFileName;
            document.getElementById('import-progress-fill').style.width = '0%';
            document.getElementById('import-progress-text').textContent = '0%';
            document.getElementById('import-trigger-btn').disabled = true;

            sessionUuid = (crypto.randomUUID && crypto.randomUUID()) ||
                'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                    var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                });

            var totalBatches = Math.ceil(importData.length / BATCH_SIZE);
            var insertados = 0, actualizados = 0;
            importErrors = [];
            var batchIndex = 0;

            function sendBatch() {
                if (batchIndex >= totalBatches) {
                    showImportResult(insertados, actualizados, importErrors);
                    return;
                }

                var start = batchIndex * BATCH_SIZE;
                var batch = importData.slice(start, start + BATCH_SIZE);

                var payload = {
                    registros: batch,
                    session_uuid: sessionUuid,
                    nombre_archivo: importFileName || null,
                    lote_index: batchIndex,
                    total_lotes: totalBatches,
                    total_registros: importData.length,
                };

                ajax(IMPORT_URL, 'POST', payload).then(function(res) {
                    insertados += res.insertados;
                    actualizados += res.actualizados;
                    if (res.errores && res.errores.length > 0) {
                        res.errores.forEach(function(e) {
                            e.fila = e.fila + start;
                            importErrors.push(e);
                        });
                    }
                    batchIndex++;
                    var pct = Math.round((batchIndex / totalBatches) * 100);
                    document.getElementById('import-progress-fill').style.width = pct + '%';
                    document.getElementById('import-progress-text').textContent = pct + '% (' + batchIndex + '/' + totalBatches + ' lotes)';
                    sendBatch();
                }).catch(function(err) {
                    alert('Error en lote ' + (batchIndex + 1) + ': ' + (err.message || 'Error desconocido'));
                    batchIndex++;
                    sendBatch();
                });
            }

            sendBatch();
        }

        function showImportResult(ins, upd, errs) {
            document.getElementById('import-modal-processing').style.display = 'none';
            document.getElementById('import-modal-done').style.display = '';
            document.getElementById('import-trigger-btn').disabled = false;

            document.getElementById('res-insertados').textContent = ins;
            document.getElementById('res-actualizados').textContent = upd;
            document.getElementById('res-errores').textContent = errs.length;

            if (errs.length > 0) {
                document.getElementById('import-errors-detail').style.display = '';
                var errTbody = document.getElementById('import-errors-tbody');
                errTbody.innerHTML = '';
                errs.forEach(function(e) {
                    var tr = document.createElement('tr');
                    tr.innerHTML = '<td>' + esc(e.fila) + '</td><td>' + esc(e.identificacion) + '</td><td>' + esc(e.error) + '</td>';
                    errTbody.appendChild(tr);
                });
            } else {
                document.getElementById('import-errors-detail').style.display = 'none';
            }

            refreshHistorial();
        }

        function closeModal() {
            document.getElementById('import-modal-overlay').style.display = 'none';
        }

        function refreshHistorial() {
            fetch(HISTORIAL_URL, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(json) {
                var contentEl = document.getElementById('historial-content');
                if (!json.data || json.data.length === 0) {
                    contentEl.innerHTML = '<p class="muted" style="margin:0;">Todavía no hay importaciones registradas.</p>';
                    return;
                }
                var html = '<div class="table-wrap"><table class="import-historial-table"><thead><tr>';
                html += '<th>Fecha y hora</th><th>Usuario</th><th>Archivo</th>';
                html += '<th class="num">Registros</th><th class="num">Insertados</th>';
                html += '<th class="num">Actualizados</th><th class="num">Errores</th><th>Estado</th>';
                html += '</tr></thead><tbody>';
                json.data.forEach(function(imp) {
                    html += '<tr>';
                    html += '<td><span class="import-historial-fecha">' + esc(imp.fecha) + '</span><span class="import-historial-hora">' + esc(imp.hora) + ' h</span></td>';
                    html += '<td>' + esc(imp.usuario) + '</td>';
                    html += '<td class="import-historial-archivo">' + esc(imp.nombre_archivo) + '</td>';
                    html += '<td class="num">' + esc(imp.total_registros) + '</td>';
                    html += '<td class="num import-historial-ok">' + esc(imp.insertados) + '</td>';
                    html += '<td class="num import-historial-upd">' + esc(imp.actualizados) + '</td>';
                    html += '<td class="num ' + (imp.con_error_raw > 0 ? 'import-historial-err' : '') + '">' + esc(imp.con_error) + '</td>';
                    html += '<td>';
                    if (imp.completada) {
                        html += '<span class="pill pill-published import-historial-pill">Completada</span>';
                    } else {
                        html += '<span class="pill pill-draft import-historial-pill">Incompleta</span>';
                    }
                    html += '</td></tr>';
                });
                html += '</tbody></table></div>';
                if (json.links) {
                    html += '<div class="import-historial-pagination">' + json.links + '</div>';
                }
                contentEl.innerHTML = html;
            })
            .catch(function() {});
        }

        function downloadErrors() {
            if (importErrors.length === 0) return;
            var csv = 'Fila,Identificacion,Error\n';
            importErrors.forEach(function(e) {
                csv += '"' + e.fila + '","' + (e.identificacion || '') + '","' + (e.error || '').replace(/"/g, '""') + '"\n';
            });
            var blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'errores_importacion.csv';
            link.click();
            URL.revokeObjectURL(link.href);
        }

        return {
            triggerFileInput: triggerFileInput,
            handleFileSelect: handleFileSelect,
            startImport: startImport,
            downloadErrors: downloadErrors,
            closeModal: closeModal,
        };
    })();
    </script>
@endsection
