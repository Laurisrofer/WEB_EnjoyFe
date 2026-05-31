<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token'])) {
    header("Location: index.php");
    exit();
}

$rol_usuario = $_SESSION['rol'];
$pagina_id = 'asistencias';
$titulo_seccion = 'Control de Asistencia';

$estilos_adicionales = '<link rel="stylesheet" href="recursos/asistencias.css">';

include 'componentes/header.php';
?>

<div class="contenedor-datos">
    <div class="card-resumen">
        <h2>Control de asistencia</h2>
        <p class="asistencia-subtitulo" id="asistencia_subtitulo">
            <?php if ($rol_usuario === 'alumno'): ?>
                Cargando estadísticas de asistencia...
            <?php else: ?>
                Consola de registro de asistencia escolar. Pasa lista en tus clases programadas de hoy o revisa los justificantes pendientes de los alumnos.
            <?php endif; ?>
        </p>
    </div>

    <!-- ==================== VISTA ALUMNO ==================== -->
    <?php if ($rol_usuario === 'alumno'): ?>
        <!-- Panel de Estadísticas -->
        <div class="contenedor-stats" id="stats_container" style="display: none;">
            <div class="stat-card">
                <h3>Total Faltas</h3>
                <div class="stat-value text-danger" id="stat_faltas">0</div>
            </div>
            <div class="stat-card">
                <h3>Total Retrasos</h3>
                <div class="stat-value text-warning" id="stat_retrasos">0</div>
            </div>
            <div class="stat-card">
                <h3>Justificadas</h3>
                <div class="stat-value text-success" id="stat_justificadas">0</div>
            </div>
            <div class="stat-card">
                <h3>Pendientes</h3>
                <div class="stat-value text-info" id="stat_pendientes">0</div>
            </div>
        </div>

        <div id="loading_alumno" class="loading-indicator">Cargando datos de asistencia...</div>

        <!-- Historial de Faltas -->
        <div id="alumno_asistencias_container" style="display: none;">
            <h3 class="historial-titulo">Historial de ausencias y retrasos</h3>
            <div class="table-responsive"><table class="tabla-asistencias">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Asignatura</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Detalle / Acciones</th>
                    </tr>
                </thead>
                <tbody id="alumno_asistencias_tbody">
                    <!-- Faltas cargadas dinámicamente -->
                </tbody>
            </table></div>
        </div>

        <!-- Modal Justificar -->
        <div id="modal_justificar" class="modal-wrapper" style="display: none;">
            <div class="modal-card">
                <h3>Justificar ausencia</h3>
                <p id="justificar_info">Asignatura el día DD/MM/AAAA</p>
                <form onsubmit="guardarJustificacion(event)">
                    <input type="hidden" id="justificar_id_input">
                    <div style="margin-bottom: 15px;">
                        <textarea id="justificar_texto_input" style="width:100%;" rows="4" required placeholder="Describe el motivo de la falta..."></textarea>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:5px;">Adjuntar archivo (opcional):</label>
                        <input type="file" id="justificar_adjunto_input" style="width:100%;">
                    </div>
                    <div class="modal-actions">
                        <button type="submit" class="btn-action">Enviar justificación</button>
                        <button type="button" class="btn-action btn-danger" onclick="cerrarModalJustificar()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

    <!-- ==================== VISTA PROFESOR / ADMIN ==================== -->
    <?php else: ?>
        <!-- Pestañas del profesor -->
        <div class="tab-nav">
            <button class="tab-link active" onclick="switchTab('tab_registro')">📋 Registrar asistencia</button>
            <button class="tab-link" onclick="switchTab('tab_justificantes')">✉️ Justificaciones Recibidas</button>
        </div>

        <!-- PESTAÑA 1: REGISTRAR ASISTENCIA -->
        <div id="tab_registro" class="view-container active">
            <!-- Selectores de Curso, Asignatura, Fecha y Sesión -->
            <div class="selector-box">
                <div class="selector-item">
                    <label for="prof_curso_select">Curso:</label>
                    <select id="prof_curso_select" onchange="onCursoChange()">
                        <option value="">-- Selecciona un curso --</option>
                    </select>
                </div>
                <div class="selector-item">
                    <label for="prof_asig_select">Asignatura:</label>
                    <select id="prof_asig_select" onchange="onAsignaturaChange()">
                        <option value="">-- Selecciona una asignatura --</option>
                    </select>
                </div>
                <div class="selector-item">
                    <label for="prof_fecha_input">Fecha:</label>
                    <input type="date" id="prof_fecha_input" onchange="onFiltroSesionChange()">
                </div>
                <div class="selector-item">
                    <label for="prof_hora_select">Hora / Sesión:</label>
                    <select id="prof_hora_select" onchange="cargarListaAlumnosAsistencia()">
                        <option value="">-- Selecciona sesión --</option>
                    </select>
                </div>
            </div>

            <!-- Listado de alumnos -->
            <div id="registro_asistencia_workspace" style="display: none;">
                <div class="card-resumen asistencia-bar-cabecera">
                    <div class="asistencia-details-title">
                        Pase de lista: <span id="asistencia_details_lbl">Curso - Asignatura</span>
                    </div>
                    <div class="asistencia-actions-bar">
                        <button class="btn-attendance active-present" onclick="marcarTodos('asistencia')">Todos Presentes</button>
                    </div>
                </div>
                
                <form onsubmit="guardarAsistenciasGrupo(event)">
                    <div class="table-responsive"><table class="tabla-asistencias">
                        <thead>
                            <tr>
                                <th>Alumno</th>
                                <th style="width: 250px;">Asistencia</th>
                                <th>Observaciones / Justificaciones</th>
                            </tr>
                        </thead>
                        <tbody id="prof_alumnos_tbody">
                            <!-- Inputs por alumno -->
                        </tbody>
                    </table></div>
                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn-action" style="width: 100%; padding:15px;">Guardar registro de asistencia</button>
                    </div>
                </form>
            </div>
            <div id="registro_asistencia_vacio" class="loading-indicator">
                Selecciona Curso, Asignatura, Fecha y Hora para pasar lista.
            </div>
        </div>

        <!-- PESTAÑA 2: JUSTIFICANTES RECIBIDOS -->
        <div id="tab_justificantes" class="view-container">
            <div id="loading_justificantes" class="loading-indicator">Cargando solicitudes de justificación...</div>
            <div id="justificantes_container" style="display: none;">
                <div class="table-responsive"><table class="tabla-asistencias">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Asignatura</th>
                            <th>Sesión / Fecha</th>
                            <th>Motivo del Alumno</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="prof_justificantes_tbody">
                        <!-- Justificaciones -->
                    </tbody>
                </table></div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    const hoy = new Date().toISOString().split('T')[0];
    
    // Configurar fecha de hoy por defecto al cargar en la vista del profesor
    document.addEventListener("DOMContentLoaded", () => {
        const fechaInput = document.getElementById('prof_fecha_input');
        if (fechaInput) fechaInput.value = hoy;
    });

    // ==================== LÓGICA ALUMNO ====================
    <?php if ($rol_usuario === 'alumno'): ?>
    function cargarAsistenciasAlumno() {
        fetch('acciones/gestion_asistencias.php?action=get_mis_asistencias')
        .then(res => {
            if (res.status === 200) return res.json();
            throw new Error('Error de conexión');
        })
        .then(data => {
            document.getElementById('loading_alumno').style.display = 'none';
            document.getElementById('stats_container').style.display = 'grid';
            document.getElementById('alumno_asistencias_container').style.display = 'block';

            // Actualizar curso
            if (data.curso) {
                document.getElementById('asistencia_subtitulo').innerHTML = `Curso matriculado: <strong>${escapeHtml(data.curso)}</strong>`;
            }

            // Actualizar contadores
            document.getElementById('stat_faltas').innerText = data.total_faltas;
            document.getElementById('stat_retrasos').innerText = data.total_retrasos;
            document.getElementById('stat_justificadas').innerText = data.justificadas;
            document.getElementById('stat_pendientes').innerText = data.pendientes;

            const tbody = document.getElementById('alumno_asistencias_tbody');
            if (data.asistencias.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; font-style:italic; color:var(--text-muted);">No tienes ausencias o retrasos registrados. ¡Buen trabajo!</td></tr>';
                return;
            }

            let html = '';
            data.asistencias.forEach(a => {
                let badgeClass = 'badge-falta';
                let estadoTexto = 'Injustificada';
                let accionHtml = '';

                if (a.tipo === 'retraso') {
                    badgeClass = 'badge-retraso';
                    estadoTexto = 'Retraso';
                }

                if (a.justificada) {
                    badgeClass = 'badge-justificada';
                    estadoTexto = 'Justificada';
                    accionHtml = `<span class="justificacion-obs-lbl">${escapeHtml(a.observaciones) || 'Verificada'}</span>`;
                } else if (a.justificante_texto) {
                    badgeClass = 'badge-pendiente';
                    estadoTexto = 'Pte. Aprobación';
                    accionHtml = `<button class="btn-link-table" onclick="mostrarModalNotif('Justificación enviada', '${escapeHtml(a.justificante_texto).replace(/'/g, "\\'")}', '${a.fecha}', 'Justificante')">Ver justificación</button>`;
                } else {
                    accionHtml = `<button class="btn-link-table" onclick="abrirModalJustificar(${a.id}, '${escapeHtml(a.asignatura).replace(/'/g, "\\'")}', '${a.fecha}', '${a.hora}')">Justificar</button>`;
                }

                html += `
                    <tr>
                        <td>${a.fecha}</td>
                        <td><strong>${a.hora}</strong></td>
                        <td>${escapeHtml(a.asignatura)}</td>
                        <td><span class="badge ${a.tipo === 'falta' ? 'badge-falta' : 'badge-retraso'}">${a.tipo}</span></td>
                        <td><span class="badge ${badgeClass}">${estadoTexto}</span></td>
                        <td>${accionHtml}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            document.getElementById('loading_alumno').innerHTML = '<div style="color:var(--danger-color)">⚠️ Error al cargar las asistencias de la base de datos.</div>';
        });
    }

    function abrirModalJustificar(id, asignatura, fecha, hora) {
        document.getElementById('justificar_id_input').value = id;
        document.getElementById('justificar_info').innerHTML = `Falta en la materia <strong>${asignatura}</strong> el día <strong>${fecha}</strong> a las <strong>${hora}</strong>.`;
        document.getElementById('justificar_texto_input').value = '';
        document.getElementById('modal_justificar').style.display = 'flex';
    }

    function cerrarModalJustificar() {
        document.getElementById('modal_justificar').style.display = 'none';
    }

    function guardarJustificacion(e) {
        e.preventDefault();
        const id = document.getElementById('justificar_id_input').value;
        const texto = document.getElementById('justificar_texto_input').value;
        const archivo = document.getElementById('justificar_adjunto_input').files[0];

        const formData = new FormData();
        formData.append('id_asistencia', id);
        formData.append('justificante_texto', texto);
        if (archivo) {
            formData.append('adjunto', archivo);
        }

        fetch('acciones/gestion_asistencias.php?action=save_justificacion', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            showToast(data.mensaje, "success");
            cerrarModalJustificar();
            cargarAsistenciasAlumno();
        })
        .catch(err => {
            console.error(err);
            showToast("Error al enviar justificante", "danger");
        });
    }

    cargarAsistenciasAlumno();
    <?php endif; ?>

    // ==================== LÓGICA PROFESOR / ADMIN ====================
    <?php if ($rol_usuario !== 'alumno'): ?>
    let dbCursos = [];
    let selectedCursoId = null;
    let selectedAsigId = null;

    function cargarCursosAsistencias() {
        // Reutilizamos el endpoint proxy de notas para obtener la lista de materias impartidas
        fetch('acciones/gestion_notas.php?action=get_notas')
        .then(res => res.json())
        .then(data => {
            dbCursos = data;
            const cursoSelect = document.getElementById('prof_curso_select');
            cursoSelect.innerHTML = '<option value="">-- Selecciona un curso --</option>' + 
                data.map(c => `<option value="${c.id}">${escapeHtml(c.nombre)}</option>`).join('');
        })
        .catch(err => console.error("Error cargando asignaturas del profesor", err));
    }

    function onCursoChange() {
        const cursoSelect = document.getElementById('prof_curso_select');
        const asigSelect = document.getElementById('prof_asig_select');
        
        selectedCursoId = parseInt(cursoSelect.value);
        selectedAsigId = null;
        
        asigSelect.innerHTML = '<option value="">-- Selecciona una asignatura --</option>';
        document.getElementById('prof_hora_select').innerHTML = '<option value="">-- Selecciona sesión --</option>';
        document.getElementById('registro_asistencia_workspace').style.display = 'none';
        document.getElementById('registro_asistencia_vacio').style.display = 'block';
        
        if (!selectedCursoId) return;

        const cursoData = dbCursos.find(c => c.id === selectedCursoId);
        if (cursoData && cursoData.asignaturas) {
            asigSelect.innerHTML += cursoData.asignaturas.map(a => 
                `<option value="${a.id}">${escapeHtml(a.nombre)}</option>`
            ).join('');
        }
    }

    function onAsignaturaChange() {
        selectedAsigId = parseInt(document.getElementById('prof_asig_select').value);
        onFiltroSesionChange();
    }

    function onFiltroSesionChange() {
        const asigSelect = document.getElementById('prof_asig_select');
        const fecha = document.getElementById('prof_fecha_input').value;
        const horaSelect = document.getElementById('prof_hora_select');
        
        document.getElementById('registro_asistencia_workspace').style.display = 'none';
        document.getElementById('registro_asistencia_vacio').style.display = 'block';
        horaSelect.innerHTML = '<option value="">-- Selecciona sesión --</option>';

        if (!selectedCursoId || !selectedAsigId || !fecha) return;

        // Cargar las sesiones de clase programadas para esa asignatura y fecha
        fetch(`acciones/gestion_asistencias.php?action=get_asistencia_curso&id_curso=${selectedCursoId}&id_asignatura=${selectedAsigId}&fecha=${fecha}`)
        .then(res => res.json())
        .then(data => {
            const sesiones = data.sesiones_programadas || [];
            
            if (sesiones.length === 0) {
                // Si no hay horario cargado en esa fecha, cargamos horas escolares estándar de apoyo (backup)
                const backupHoras = ["08:00", "09:00", "10:00", "11:30", "12:30", "13:30"];
                horaSelect.innerHTML = '<option value="">-- Selecciona sesión (Manual) --</option>' +
                    backupHoras.map(h => `<option value="${h}">${h} - ${sumarUnaHora(h)}</option>`).join('');
            } else {
                horaSelect.innerHTML = '<option value="">-- Selecciona sesión (Horario) --</option>' +
                    sesiones.map(s => `<option value="${s.hora_inicio}">${s.hora_inicio} - ${s.hora_fin}</option>`).join('');
                
                // Autoseleccionar la primera sesión si la hay
                horaSelect.value = sesiones[0].hora_inicio;
                cargarListaAlumnosAsistencia();
            }
        })
        .catch(err => console.error("Error al obtener sesiones programadas", err));
    }

    function sumarUnaHora(horaStr) {
        const partes = horaStr.split(':');
        const h = parseInt(partes[0]) + 1;
        return `${h < 10 ? '0' + h : h}:${partes[1]}`;
    }

    function cargarListaAlumnosAsistencia() {
        const fecha = document.getElementById('prof_fecha_input').value;
        const hora = document.getElementById('prof_hora_select').value;
        
        if (!selectedCursoId || !selectedAsigId || !fecha || !hora) {
            document.getElementById('registro_asistencia_workspace').style.display = 'none';
            document.getElementById('registro_asistencia_vacio').style.display = 'block';
            return;
        }

        // Carga la lista de alumnos y sus asistencias en la sesión actual
        fetch(`acciones/gestion_asistencias.php?action=get_asistencia_curso&id_curso=${selectedCursoId}&id_asignatura=${selectedAsigId}&fecha=${fecha}&hora=${hora}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('registro_asistencia_vacio').style.display = 'none';
            document.getElementById('registro_asistencia_workspace').style.display = 'block';

            const cursoNombre = document.getElementById('prof_curso_select').options[document.getElementById('prof_curso_select').selectedIndex].text;
            const asigNombre = document.getElementById('prof_asig_select').options[document.getElementById('prof_asig_select').selectedIndex].text;
            document.getElementById('asistencia_details_lbl').innerText = `${cursoNombre} - ${asigNombre} (${hora})`;

            const tbody = document.getElementById('prof_alumnos_tbody');
            if (data.alumnos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align:center; font-style:italic; color:var(--text-muted);">No hay alumnos matriculados en esta asignatura.</td></tr>';
                return;
            }

            let html = '';
            data.alumnos.forEach(al => {
                const as = al.asistencia;
                const tipo = as.tipo; // 'asistencia', 'falta', 'retraso'
                const isJustificada = as.justificada;
                
                // Mostrar badge de justificante si tiene y está justificado/pendiente
                let badgeHtml = '';
                if (isJustificada) {
                    badgeHtml = ' <span class="badge badge-justificada" style="font-size:0.7em;">Justificada</span>';
                } else if (as.justificante_texto) {
                    badgeHtml = ` <button type="button" class="btn-link-table" style="font-size:0.75em;" onclick="mostrarModalNotif('Justificante de ${escapeHtml(al.nombre).replace(/'/g, "\\'")}', '${escapeHtml(as.justificante_texto).replace(/'/g, "\\'")}', '', 'Revisar')">Revisar Justificante ✉️</button>`;
                }

                html += `
                    <tr>
                        <td><strong>${escapeHtml(al.nombre)}</strong>${badgeHtml}</td>
                        <td>
                            <div class="btn-group-attendance" data-alumno="${al.id_alumno}">
                                <button type="button" class="btn-attendance ${tipo === 'asistencia' ? 'active-present' : ''}" onclick="setAttendanceStatus(${al.id_alumno}, 'asistencia')">Presente</button>
                                <button type="button" class="btn-attendance ${tipo === 'falta' ? 'active-falta' : ''}" onclick="setAttendanceStatus(${al.id_alumno}, 'falta')">Falta</button>
                                <button type="button" class="btn-attendance ${tipo === 'retraso' ? 'active-retraso' : ''}" onclick="setAttendanceStatus(${al.id_alumno}, 'retraso')">Retraso</button>
                            </div>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <input type="text" class="input-obs-asistencia input-obs-asistencia-field" data-alumno="${al.id_alumno}" placeholder="Ej. Retraso de tren, indisposición..." value="${escapeHtml(as.observaciones)}">
                                <label class="label-just-asistencia">
                                    <input type="checkbox" class="input-just-asistencia" data-alumno="${al.id_alumno}" ${isJustificada ? 'checked' : ''}> Justificada
                                </label>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        })
        .catch(err => console.error("Error al obtener la lista de alumnos", err));
    }

    function setAttendanceStatus(idAlumno, tipo) {
        const group = document.querySelector(`.btn-group-attendance[data-alumno="${idAlumno}"]`);
        if (!group) return;
        
        group.querySelectorAll('.btn-attendance').forEach(b => {
            b.classList.remove('active-present', 'active-falta', 'active-retraso');
        });

        const btn = Array.from(group.querySelectorAll('.btn-attendance')).find(b => b.innerText.toLowerCase().includes(tipo === 'asistencia' ? 'presente' : tipo));
        if (btn) {
            if (tipo === 'asistencia') btn.classList.add('active-present');
            else if (tipo === 'falta') btn.classList.add('active-falta');
            else if (tipo === 'retraso') btn.classList.add('active-retraso');
        }
    }

    function marcarTodos(tipo) {
        document.querySelectorAll('.btn-group-attendance').forEach(group => {
            const idAlumno = group.getAttribute('data-alumno');
            setAttendanceStatus(idAlumno, tipo);
        });
    }

    function guardarAsistenciasGrupo(e) {
        e.preventDefault();
        const fecha = document.getElementById('prof_fecha_input').value;
        const hora = document.getElementById('prof_hora_select').value;
        
        const rows = document.querySelectorAll('.btn-group-attendance');
        const asistencias = [];

        rows.forEach(row => {
            const idAlumno = parseInt(row.getAttribute('data-alumno'));
            
            // Buscar cuál botón está seleccionado
            let tipo = 'asistencia';
            if (row.querySelector('.btn-attendance.active-falta')) tipo = 'falta';
            else if (row.querySelector('.btn-attendance.active-retraso')) tipo = 'retraso';

            // Observaciones y justificaciones
            const obsInput = document.querySelector(`.input-obs-asistencia[data-alumno="${idAlumno}"]`);
            const observaciones = obsInput ? obsInput.value : '';

            const justCheck = document.querySelector(`.input-just-asistencia[data-alumno="${idAlumno}"]`);
            const justificada = justCheck ? justCheck.checked : false;

            asistencias.push({
                id_alumno: idAlumno,
                tipo: tipo,
                justificada: justificada,
                observaciones: observaciones
            });
        });

        const payload = {
            id_asignatura: selectedAsigId,
            fecha: fecha,
            hora: hora,
            asistencias: asistencias
        };

        fetch('acciones/gestion_asistencias.php?action=save_asistencias', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            showToast(data.mensaje, "success");
            cargarListaAlumnosAsistencia();
        })
        .catch(err => {
            console.error(err);
            showToast("Error al registrar asistencia", "danger");
        });
    }

    // --- PESTAÑA 2: JUSTIFICANTES RECIBIDOS ---
    function cargarJustificacionesRecibidas() {
        document.getElementById('loading_justificantes').style.display = 'block';
        document.getElementById('justificantes_container').style.display = 'none';

        fetch('acciones/gestion_asistencias.php?action=get_justificaciones')
        .then(res => res.json())
        .then(data => {
            document.getElementById('loading_justificantes').style.display = 'none';
            document.getElementById('justificantes_container').style.display = 'block';

            const tbody = document.getElementById('prof_justificantes_tbody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; font-style:italic; color:var(--text-muted);">No hay justificaciones pendientes de revisión.</td></tr>';
                return;
            }

            let html = '';
            data.forEach(j => {
                html += `
                    <tr>
                        <td><strong>${escapeHtml(j.alumno)}</strong></td>
                        <td>${escapeHtml(j.asignatura)}</td>
                        <td>${j.fecha} a las <strong>${j.hora}</strong> (${j.tipo === 'falta' ? 'Falta' : 'Retraso'})</td>
                        <td style="font-size:0.9em; max-width:300px; color:var(--text-muted);">${escapeHtml(j.justificante)}</td>
                        <td>
                            <div style="display:flex; gap:10px;">
                                <button class="btn-attendance active-present" onclick="resolverJustificante(${j.id}, 'aprobar')">Aprobar</button>
                                <button class="btn-attendance active-falta" onclick="resolverJustificante(${j.id}, 'rechazar')">Rechazar</button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        })
        .catch(err => console.error("Error al obtener justificantes", err));
    }

    function resolverJustificante(idAsistencia, resolucion) {
        fetch('acciones/gestion_asistencias.php?action=resolver_justificacion', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_asistencia: idAsistencia, resolucion: resolucion })
        })
        .then(res => res.json())
        .then(data => {
            showToast(data.mensaje, "success");
            cargarJustificacionesRecibidas();
        })
        .catch(err => {
            console.error(err);
            showToast("Error al procesar justificante", "danger");
        });
    }

    function switchTab(tabId) {
        document.querySelectorAll('.tab-link').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.view-container').forEach(view => view.classList.remove('active'));
        
        // Activar botón de pestaña pulsado
        const tabBtn = Array.from(document.querySelectorAll('.tab-link')).find(b => b.outerHTML.includes(tabId));
        if (tabBtn) tabBtn.classList.add('active');
        
        document.getElementById(tabId).classList.add('active');

        if (tabId === 'tab_justificantes') {
            cargarJustificacionesRecibidas();
        } else if (tabId === 'tab_registro') {
            cargarCursosAsistencias();
        }
    }

    cargarCursosAsistencias();
    <?php endif; ?>
</script>

</body>
</html>

