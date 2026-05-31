<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token'])) {
    header("Location: index.php");
    exit();
}

$rol_usuario = $_SESSION['rol'];
$pagina_id = 'notas';
$titulo_seccion = 'Calificaciones';

$estilos_adicionales = '<link rel="stylesheet" href="recursos/notas.css">';

include 'componentes/header.php';
?>

<div class="contenedor-datos">
    <div class="card-resumen">
        <h2>Boletín de calificaciones</h2>
        <p class="boletin-subtitulo" id="boletin_subtitulo">
            <?php if ($rol_usuario === 'alumno'): ?>
                Cargando curso matriculado...
            <?php else: ?>
                Consola de calificaciones para docentes y administración. Registra notas de entregas individuales por alumno o actividades globales por curso.
            <?php endif; ?>
        </p>
    </div>

    <!-- ==================== VISTA ALUMNO ==================== -->
    <?php if ($rol_usuario === 'alumno'): ?>
        <div id="loading_alumno" class="loading-indicator">Cargando calificaciones del boletín...</div>
        <div id="alumno_container" class="asig-list" style="display: none;">
            <!-- Cabecera simulada de la tabla -->
            <div class="tabla-cabecera-alumno">
                <div>Asignatura</div>
                <div style="text-align: center;">1ª Eval</div>
                <div style="text-align: center;">2ª Eval</div>
                <div style="text-align: center;">3ª Eval</div>
                <div style="text-align: center;">Final</div>
                <div style="text-align: center;">Observaciones</div>
                <div></div>
            </div>
            
            <div id="asignaturas_acordeon_list"></div>

            <!-- Botón imprimir boletín -->
            <div style="text-align: right; margin-top: 20px;">
                <button onclick="window.print()" class="btn-action" style="display:inline-flex; align-items:center; gap:8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                    Imprimir boletín
                </button>
            </div>
        </div>

    <!-- ==================== VISTA PROFESOR / ADMIN ==================== -->
    <?php else: ?>
        <!-- Selectores de Curso y Asignatura -->
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
        </div>

        <div id="editor_notas_container" style="display: none;">
            <!-- Tabs para alternar flujos de trabajo -->
            <div class="tab-nav">
                <button class="tab-link active" onclick="switchTab('tab_alumno')">👤 Calificar por alumno</button>
                <button class="tab-link" onclick="switchTab('tab_actividad')">📝 Calificar por actividad</button>
            </div>

            <!-- PESTAÑA 1: CALIFICAR POR ALUMNO -->
            <div id="tab_alumno" class="view-container active">
                <div class="student-select-list" id="student_bubbles_container">
                    <!-- Burbujas de alumnos cargadas dinámicamente -->
                </div>

                <div id="grade_student_workspace" style="display: none;">
                    <div class="workspace-layout">
                        <!-- Ficha de calificaciones actuales del alumno -->
                        <div class="workspace-main">
                            <div class="form-box">
                                <h3 id="workspace_student_name">Calificaciones de Alumno</h3>
                                <div class="table-responsive"><table class="subtable">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Evaluación</th>
                                            <th>Actividad</th>
                                            <th>Nota</th>
                                            <th>Comentario</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="student_grades_tbody">
                                        <!-- Notas individuales -->
                                    </tbody>
                                </table></div>
                            </div>
                        </div>

                        <!-- Panel lateral para añadir/editar notas -->
                        <div class="workspace-sidebar">
                            <div class="form-box">
                                <h3 id="form_calif_title">Añadir calificación</h3>
                                <form id="form_guardar_calif" onsubmit="guardarCalificacionIndividual(event)">
                                    <input type="hidden" id="calif_id_input">
                                    <div class="form-col" style="margin-bottom: 15px;">
                                        <label for="calif_actividad">Nombre de la actividad:</label>
                                        <input type="text" id="calif_actividad" required placeholder="Ej: Práctica 2, Examen Matrices">
                                    </div>
                                    <div class="form-row">
                                        <div class="form-col">
                                            <label for="calif_eval">Evaluación:</label>
                                            <select id="calif_eval" required>
                                                <option value="1">1ª Evaluación</option>
                                                <option value="2">2ª Evaluación</option>
                                                <option value="3">3ª Evaluación</option>
                                            </select>
                                        </div>
                                        <div class="form-col">
                                            <label for="calif_nota">Calificación (0-10):</label>
                                            <input type="number" id="calif_nota" required step="0.01" min="0" max="10" placeholder="Ej: 8.5">
                                        </div>
                                    </div>
                                    <div class="form-col" style="margin-bottom: 15px;">
                                        <label for="calif_fecha">Fecha:</label>
                                        <input type="date" id="calif_fecha" required>
                                    </div>
                                    <div class="form-col" style="margin-bottom: 20px;">
                                        <label for="calif_comentario">Comentario / Feedback:</label>
                                        <textarea id="calif_comentario" rows="3" placeholder="Comentarios sobre la corrección..."></textarea>
                                    </div>
                                    <div style="display: flex; gap: 10px;">
                                        <button type="submit" class="btn-action" style="flex: 1;">Guardar nota</button>
                                        <button type="button" id="btn_cancelar_edicion" onclick="resetFormCalif()" class="btn-action btn-danger" style="display: none;">Cancelar</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Nota Final y Observaciones Globales -->
                            <div class="form-box">
                                <h3>Nota final y acta</h3>
                                <form onsubmit="guardarNotaFinal(event)">
                                    <div class="form-row">
                                        <div class="form-col">
                                            <label for="final_nota">Nota final de curso:</label>
                                            <input type="number" id="final_nota" step="0.1" min="0" max="10" placeholder="Aún sin evaluar">
                                        </div>
                                    </div>
                                    <div class="form-col" style="margin-bottom: 20px;">
                                        <label for="final_obs">Observaciones globales del boletín:</label>
                                        <textarea id="final_obs" rows="3" placeholder="Resumen del rendimiento del alumno en el curso..."></textarea>
                                    </div>
                                    <button type="submit" class="btn-action" style="width: 100%;">Guardar acta final</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PESTAÑA 2: CALIFICAR POR ACTIVIDAD -->
            <div id="tab_actividad" class="view-container">
                <div class="form-box">
                    <h3>Nueva actividad conjunta</h3>
                    <form onsubmit="guardarCalificacionActividadGrupo(event)">
                        <div class="form-row" style="margin-bottom: 20px;">
                            <div class="form-col">
                                <label for="grupo_actividad">Nombre de la actividad:</label>
                                <input type="text" id="grupo_actividad" required placeholder="Ej: Práctica Grupal, Examen Bloque 1">
                            </div>
                            <div class="form-col">
                                <label for="grupo_eval">Evaluación:</label>
                                <select id="grupo_eval" required>
                                    <option value="1">1ª Evaluación</option>
                                    <option value="2">2ª Evaluación</option>
                                    <option value="3">3ª Evaluación</option>
                                </select>
                            </div>
                            <div class="form-col">
                                <label for="grupo_fecha">Fecha:</label>
                                <input type="date" id="grupo_fecha" required>
                            </div>
                        </div>

                        <p style="font-weight: bold; color: var(--text-color); margin-bottom: 12px; border-bottom: 1.5px solid var(--border-color); padding-bottom: 5px;">Calificaciones de los alumnos:</p>
                        
                        <div style="max-height: 400px; overflow-y: auto; padding-right: 5px; margin-bottom: 25px;">
                            <div class="table-responsive"><table class="subtable">
                                <thead>
                                    <tr>
                                        <th>Alumno</th>
                                        <th style="width: 150px;">Nota (0-10)</th>
                                        <th>Comentario de feedback</th>
                                    </tr>
                                </thead>
                                <tbody id="grupo_alumnos_tbody">
                                    <!-- Inputs dinámicos por alumno -->
                                </tbody>
                            </table></div>
                        </div>
                        
                        <button type="submit" class="btn-action" style="width: 100%;">Guardar calificaciones del grupo</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Configuración inicial de fecha actual para los formularios
    const hoy = new Date().toISOString().split('T')[0];
    document.addEventListener("DOMContentLoaded", () => {
        const fechaInputs = document.querySelectorAll('input[type="date"]');
        fechaInputs.forEach(input => input.value = hoy);
    });

    // ==================== FUNCIONES AUXILIARES DE EVALUACIÓN ====================
    // Extrae el número de evaluación (1, 2 o 3) del nombre de actividad
    function extraerEvaluacion(actividad) {
        // Formato nuevo: [1] Actividad, [2] Actividad, [3] Actividad
        if (actividad.startsWith('[1] ')) return '1';
        if (actividad.startsWith('[2] ')) return '2';
        if (actividad.startsWith('[3] ')) return '3';
        // Formato antiguo: [1ª Eval] Actividad
        if (actividad.startsWith('[1ª Eval] ')) return '1';
        if (actividad.startsWith('[2ª Eval] ')) return '2';
        if (actividad.startsWith('[3ª Eval] ')) return '3';
        // Fallback con regex genérico
        const match = actividad.match(/^\[(\d+)[^\]]*\]\s*/);
        if (match) return match[1];
        return '-';
    }

    // Limpia el nombre de actividad quitando el prefijo [X] o [Xª Eval]
    function limpiarNombreActividad(actividad) {
        return actividad.replace(/^\[[^\]]*\]\s*/, '');
    }

    // ==================== LÓGICA ALUMNO ====================
    <?php if ($rol_usuario === 'alumno'): ?>
    function cargarNotasAlumno() {
        fetch('acciones/gestion_notas.php?action=get_mis_notas')
        .then(res => {
            if (res.status === 200) return res.json();
            throw new Error('Error al cargar');
        })
        .then(data => {
            document.getElementById('loading_alumno').style.display = 'none';
            document.getElementById('alumno_container').style.display = 'block';
            
            // Actualizar curso matriculado
            const subtitulo = document.getElementById('boletin_subtitulo');
            if (subtitulo && data.curso) {
                subtitulo.innerHTML = `Curso matriculado: <strong>${escapeHtml(data.curso)}</strong>`;
            }
            
            const listContainer = document.getElementById('asignaturas_acordeon_list');
            const asignaturas = data.asignaturas || [];
            if (asignaturas.length === 0) {
                listContainer.innerHTML = '<div class="loading-indicator">No estás matriculado en ninguna asignatura.</div>';
                return;
            }

            let listHtml = '';
            asignaturas.forEach(m => {
                // Separar calificaciones en evaluaciones
                let califs1 = [], califs2 = [], califs3 = [];
                m.calificaciones.forEach(c => {
                    const evalNum = extraerEvaluacion(c.actividad);
                    if (evalNum === '1') califs1.push(c);
                    else if (evalNum === '2') califs2.push(c);
                    else if (evalNum === '3') califs3.push(c);
                });

                // Calcular medias aritméticas
                const calcMedia = (arr) => {
                    if (arr.length === 0) return '-';
                    const sum = arr.reduce((acc, curr) => acc + (curr.nota || 0), 0);
                    return (sum / arr.length).toFixed(2);
                };

                const media1 = calcMedia(califs1);
                const media2 = calcMedia(califs2);
                const media3 = calcMedia(califs3);
                const finalNota = m.nota_final !== null ? m.nota_final.toFixed(1) : '-';

                // Determinar estilos de color (rojo si < 5.0)
                const getNotaStyle = (notaStr) => {
                    if (notaStr === '-') return '';
                    return parseFloat(notaStr) < 5.0 ? 'color: var(--danger-color); font-weight: bold;' : '';
                };

                const styleMedia1 = getNotaStyle(media1);
                const styleMedia2 = getNotaStyle(media2);
                const styleMedia3 = getNotaStyle(media3);
                const styleFinal = finalNota !== '-' && parseFloat(finalNota) < 5.0 
                    ? 'color: var(--danger-color); font-weight: bold;' 
                    : 'color: var(--primary-color); font-weight: bold;';
                
                // Observaciones
                const hasObs = m.observaciones_globales.trim() !== "";
                const obsBtn = hasObs 
                    ? `<button class="feedback-bubble" onclick="event.stopPropagation(); mostrarModalNotif('Observaciones globales de ${m.asignatura}', '${m.observaciones_globales.replace(/'/g, "\\'")}', '', 'Observaciones')">Ver</button>` 
                    : '<span style="color:var(--text-muted)">-</span>';

                // Generar subtabla
                let subTableRows = '';
                if (m.calificaciones.length === 0) {
                    subTableRows = '<tr><td colspan="5" style="text-align:center; font-style:italic; color:var(--text-muted);">Aún no tienes tareas calificadas en esta asignatura.</td></tr>';
                } else {
                    m.calificaciones.forEach(c => {
                        let evalLabel = extraerEvaluacion(c.actividad);
                        let cleanName = limpiarNombreActividad(c.actividad);

                        const hasComment = c.comentario.trim() !== "";
                        const commentHtml = hasComment 
                            ? `<button class="feedback-bubble" onclick="event.stopPropagation(); mostrarModalNotif('Observaciones: ${cleanName}', '${c.comentario.replace(/'/g, "\\'")}', '${c.fecha}', 'Observaciones')">Leer</button>`
                            : '<span style="color:var(--text-muted)">Sin observaciones</span>';

                        const styleNotaPart = c.nota !== null && c.nota < 5.0 
                            ? 'color: var(--danger-color);' 
                            : 'color: var(--primary-color);';

                        subTableRows += `
                            <tr>
                                <td>${c.fecha}</td>
                                <td><span style="font-weight:bold; font-size:0.9em;">${evalLabel}</span></td>
                                <td><strong>${escapeHtml(cleanName)}</strong></td>
                                <td><span style="font-weight:bold; ${styleNotaPart}">${c.nota !== null ? c.nota.toFixed(2) : '-'}</span></td>
                                <td>${commentHtml}</td>
                            </tr>
                        `;
                    });
                }

                listHtml += `
                    <div class="asig-row" id="asig_row_${m.id_matricula}">
                        <div class="asig-summary" onclick="toggleAsigRow(${m.id_matricula})">
                            <h3>${escapeHtml(m.asignatura)}<br><span class="profesor-subtexto">Profesor: ${escapeHtml(m.profesor)}</span></h3>
                            <div class="eval-col" style="${styleMedia1}">${media1}</div>
                            <div class="eval-col" style="${styleMedia2}">${media2}</div>
                            <div class="eval-col" style="${styleMedia3}">${media3}</div>
                            <div class="final-col" style="${styleFinal}">${finalNota}</div>
                            <div class="obs-col">${obsBtn}</div>
                            <div class="arrow-col">▼</div>
                        </div>
                        <div class="asig-details">
                            <div class="desglose-container">
                                <h4 style="margin-top:0; margin-bottom:15px; color:var(--text-color);">Detalle de calificaciones parciales</h4>
                                <div class="table-responsive"><table class="subtable">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Evaluación</th>
                                            <th>Actividad</th>
                                            <th>Nota</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${subTableRows}
                                    </tbody>
                                </table></div>
                            </div>
                        </div>
                    </div>
                `;
            });
            listContainer.innerHTML = listHtml;
        })
        .catch(err => {
            console.error(err);
            document.getElementById('loading_alumno').innerHTML = '<div style="color:var(--danger-color)">⚠️ Error al conectar con el servidor. Por favor, inténtalo de nuevo.</div>';
        });
    }

    function toggleAsigRow(idMatricula) {
        const row = document.getElementById(`asig_row_${idMatricula}`);
        if (row) {
            row.classList.toggle('open');
        }
    }

    cargarNotasAlumno();
    <?php endif; ?>

    // ==================== LÓGICA PROFESOR / ADMIN ====================
    <?php if ($rol_usuario !== 'alumno'): ?>
    let dbData = []; // Caché de cursos -> asignaturas -> alumnos -> calificaciones
    let selectedCursoId = null;
    let selectedAsigId = null;
    let selectedMatriculaId = null; // Alumno seleccionado actualmente en Workspace

    function cargarNotasProfesores() {
        fetch('acciones/gestion_notas.php?action=get_notas')
        .then(res => {
            if (res.status === 200) return res.json();
            throw new Error('Error');
        })
        .then(data => {
            dbData = data;
            
            // Poblar selector de cursos
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
        selectedMatriculaId = null;
        
        document.getElementById('editor_notas_container').style.display = 'none';
        asigSelect.innerHTML = '<option value="">-- Selecciona una asignatura --</option>';
        
        if (!selectedCursoId) return;

        const cursoData = dbData.find(c => c.id === selectedCursoId);
        if (cursoData && cursoData.asignaturas) {
            asigSelect.innerHTML += cursoData.asignaturas.map(a => 
                `<option value="${a.id}">${escapeHtml(a.nombre)}</option>`
            ).join('');
        }
    }

    function onAsignaturaChange() {
        const asigSelect = document.getElementById('prof_asig_select');
        selectedAsigId = parseInt(asigSelect.value);
        selectedMatriculaId = null;
        
        if (!selectedAsigId) {
            document.getElementById('editor_notas_container').style.display = 'none';
            return;
        }

        document.getElementById('editor_notas_container').style.display = 'block';
        
        // Cargar alumnos e iniciar Workspace
        poblarAlumnosAsignatura();
        poblarTablaCalificacionGrupo();
    }

    function switchTab(tabId) {
        document.querySelectorAll('.tab-link').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.view-container').forEach(view => view.classList.remove('active'));
        
        // Marcar botón activo
        const btn = Array.from(document.querySelectorAll('.tab-link')).find(b => b.outerHTML.includes(tabId));
        if (btn) btn.classList.add('active');
        
        document.getElementById(tabId).classList.add('active');
    }

    function getAlumnosActuales() {
        if (!selectedCursoId || !selectedAsigId) return [];
        const curso = dbData.find(c => c.id === selectedCursoId);
        if (!curso) return [];
        const asig = curso.asignaturas.find(a => a.id === selectedAsigId);
        return asig ? asig.alumnos : [];
    }

    // --- PESTAÑA 1: CALIFICAR POR ALUMNO ---
    function poblarAlumnosAsignatura() {
        const container = document.getElementById('student_bubbles_container');
        const alumnos = getAlumnosActuales();
        
        if (alumnos.length === 0) {
            container.innerHTML = '<div style="color:var(--text-muted); font-style:italic;">No hay alumnos matriculados en esta asignatura.</div>';
            document.getElementById('grade_student_workspace').style.display = 'none';
            return;
        }

        container.innerHTML = alumnos.map(a => `
            <div class="student-bubble" id="student_bubble_${a.id_matricula}" onclick="seleccionarAlumnoWorkspace(${a.id_matricula})">
                ${escapeHtml(a.nombre)}
            </div>
        `).join('');

        // Seleccionar automáticamente al primer alumno
        seleccionarAlumnoWorkspace(alumnos[0].id_matricula);
    }

    function seleccionarAlumnoWorkspace(idMatricula) {
        selectedMatriculaId = idMatricula;
        
        // Marcar burbuja activa
        document.querySelectorAll('.student-bubble').forEach(b => b.classList.remove('active'));
        const activeBubble = document.getElementById(`student_bubble_${idMatricula}`);
        if (activeBubble) activeBubble.classList.add('active');
        
        document.getElementById('grade_student_workspace').style.display = 'block';

        // Obtener datos del alumno
        const alumnos = getAlumnosActuales();
        const alumno = alumnos.find(a => a.id_matricula === idMatricula);
        if (!alumno) return;

        document.getElementById('workspace_student_name').innerText = `Calificaciones de: ${alumno.nombre}`;
        
        // Cargar notas en la subtabla
        poblarHistorialNotasAlumno(alumno);
        
        // Rellenar formulario de Nota Final
        document.getElementById('final_nota').value = alumno.nota_final !== null ? alumno.nota_final : '';
        document.getElementById('final_obs').value = alumno.observaciones_globales || '';
        
        resetFormCalif();
    }

    function poblarHistorialNotasAlumno(alumno) {
        const tbody = document.getElementById('student_grades_tbody');
        if (!alumno.calificaciones || alumno.calificaciones.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; font-style:italic; color:var(--text-muted);">Sin calificaciones añadidas.</td></tr>';
            return;
        }

        let html = '';
        alumno.calificaciones.forEach(c => {
            let evalLabel = extraerEvaluacion(c.actividad);
            let cleanName = limpiarNombreActividad(c.actividad);

            html += `
                <tr>
                    <td>${c.fecha}</td>
                    <td><span style="font-weight:bold; font-size:0.9em;">${evalLabel}</span></td>
                    <td><strong>${escapeHtml(cleanName)}</strong></td>
                    <td><span style="font-weight:bold; color:var(--primary-color);">${c.nota.toFixed(2)}</span></td>
                    <td style="font-size:0.85em; color:var(--text-muted);">${escapeHtml(c.comentario) || '-'}</td>
                    <td>
                        <button onclick="prepararEdicionNota(${c.id}, '${escapeHtml(cleanName).replace(/'/g, "\\'")}', '${evalLabel}', ${c.nota}, '${escapeHtml(c.comentario).replace(/'/g, "\\'")}', '${c.fecha}')" style="background:none; border:none; cursor:pointer;" title="Editar">✏️</button>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function prepararEdicionNota(id, actividad, evaluacion, nota, comentario, fecha) {
        document.getElementById('calif_id_input').value = id;
        document.getElementById('calif_actividad').value = actividad;
        document.getElementById('calif_eval').value = evaluacion;
        document.getElementById('calif_nota').value = nota;
        document.getElementById('calif_comentario').value = comentario;
        
        // Convertir fecha de d/m/Y a Y-m-d
        const partes = fecha.split('/');
        document.getElementById('calif_fecha').value = `${partes[2]}-${partes[1]}-${partes[0]}`;
        
        document.getElementById('form_calif_title').innerText = "Editar calificación";
        document.getElementById('btn_cancelar_edicion').style.display = 'block';
    }

    function resetFormCalif() {
        document.getElementById('calif_id_input').value = '';
        document.getElementById('form_guardar_calif').reset();
        document.getElementById('calif_fecha').value = hoy;
        document.getElementById('form_calif_title').innerText = "Añadir calificación";
        document.getElementById('btn_cancelar_edicion').style.display = 'none';
    }

    function guardarCalificacionIndividual(e) {
        e.preventDefault();
        
        const idCalif = document.getElementById('calif_id_input').value;
        const actividad = document.getElementById('calif_actividad').value;
        const evaluacion = document.getElementById('calif_eval').value;
        const nota = parseFloat(document.getElementById('calif_nota').value);
        const comentario = document.getElementById('calif_comentario').value;
        const fecha = document.getElementById('calif_fecha').value;

        // Validar nota
        if (isNaN(nota) || nota < 0 || nota > 10) {
            showToast("La nota debe ser un número entre 0 y 10", "danger");
            return;
        }

        // Prefijar la evaluación al nombre de la actividad
        const actividadPrefijada = `[${evaluacion}] ${actividad}`;

        const datos = {
            id_matricula: selectedMatriculaId,
            id_calificacion: idCalif !== "" ? parseInt(idCalif) : null,
            nombre_actividad: actividadPrefijada,
            nota: nota,
            comentario: comentario,
            fecha: fecha
        };

        fetch('acciones/gestion_notas.php?action=save_calif', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        })
        .then(res => res.json())
        .then(resData => {
            showToast(resData.mensaje, "success");
            
            // Recargar datos locales
            recargarCalificacionesYActualizar(() => {
                seleccionarAlumnoWorkspace(selectedMatriculaId);
            });
        })
        .catch(err => {
            console.error(err);
            showToast("Error al guardar la calificación", "danger");
        });
    }

    function guardarNotaFinal(e) {
        e.preventDefault();
        const notaInput = document.getElementById('final_nota').value;
        const observaciones = document.getElementById('final_obs').value;
        
        const nota = notaInput !== "" ? parseFloat(notaInput) : null;
        if (nota !== null && (nota < 0 || nota > 10)) {
            showToast("La nota final debe estar entre 0 y 10", "danger");
            return;
        }

        const datos = {
            id_matricula: selectedMatriculaId,
            nota_final: nota,
            observaciones_globales: observaciones
        };

        fetch('acciones/gestion_notas.php?action=save_final', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        })
        .then(res => res.json())
        .then(resData => {
            showToast(resData.mensaje, "success");
            
            // Recargar datos
            recargarCalificacionesYActualizar(() => {
                seleccionarAlumnoWorkspace(selectedMatriculaId);
            });
        })
        .catch(err => {
            console.error(err);
            showToast("Error al registrar acta final", "danger");
        });
    }

    // --- PESTAÑA 2: CALIFICAR POR ACTIVIDAD ---
    function poblarTablaCalificacionGrupo() {
        const tbody = document.getElementById('grupo_alumnos_tbody');
        const alumnos = getAlumnosActuales();
        
        if (alumnos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" style="text-align:center; font-style:italic; color:var(--text-muted);">No hay alumnos en el curso para calificar.</td></tr>';
            return;
        }

        tbody.innerHTML = alumnos.map(a => `
            <tr>
                <td><strong>${escapeHtml(a.nombre)}</strong></td>
                <td>
                    <input type="number" class="grupo-nota-input" data-matricula="${a.id_matricula}" step="0.01" min="0" max="10" placeholder="Nota (0-10)" style="width:110px; padding:6px; border-radius:4px; border:1px solid var(--input-border); background-color:var(--input-bg); color:var(--text-color);">
                </td>
                <td>
                    <input type="text" class="grupo-comentario-input" data-matricula="${a.id_matricula}" placeholder="Comentarios..." style="width:100%; max-width:400px; padding:6px; border-radius:4px; border:1px solid var(--input-border); background-color:var(--input-bg); color:var(--text-color);">
                </td>
            </tr>
        `).join('');
    }

    function guardarCalificacionActividadGrupo(e) {
        e.preventDefault();
        
        const actividad = document.getElementById('grupo_actividad').value;
        const evaluacion = document.getElementById('grupo_eval').value;
        const fecha = document.getElementById('grupo_fecha').value;

        const notaInputs = document.querySelectorAll('.grupo-nota-input');
        
        // Recopilamos todas las promesas para guardarlas una a una en la API
        const promesasGuardado = [];
        let algunError = false;

        notaInputs.forEach(input => {
            const notaVal = input.value;
            // Si está vacío, no calificamos a este alumno
            if (notaVal === "") return;

            const nota = parseFloat(notaVal);
            if (nota < 0 || nota > 10) {
                algunError = true;
                return;
            }

            const idMatricula = parseInt(input.getAttribute('data-matricula'));
            const comentarioInput = document.querySelector(`.grupo-comentario-input[data-matricula="${idMatricula}"]`);
            const comentario = comentarioInput ? comentarioInput.value : '';

            const actividadPrefijada = `[${evaluacion}] ${actividad}`;

            const datos = {
                id_matricula: idMatricula,
                id_calificacion: null, // Siempre nueva
                nombre_actividad: actividadPrefijada,
                nota: nota,
                comentario: comentario,
                fecha: fecha
            };

            const req = fetch('acciones/gestion_notas.php?action=save_calif', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });
            promesasGuardado.push(req);
        });

        if (algunError) {
            showToast("Hay notas con valores inválidos (deben estar entre 0 y 10)", "danger");
            return;
        }

        if (promesasGuardado.length === 0) {
            showToast("No has introducido notas para ningún alumno", "warning");
            return;
        }

        Promise.all(promesasGuardado)
        .then(() => {
            showToast("Calificaciones grupales registradas", "success");
            document.getElementById('grupo_actividad').value = '';
            document.getElementById('grupo_fecha').value = hoy;
            
            // Limpiar inputs
            document.querySelectorAll('.grupo-nota-input').forEach(i => i.value = '');
            document.querySelectorAll('.grupo-comentario-input').forEach(i => i.value = '');

            // Recargar datos
            recargarCalificacionesYActualizar(() => {
                if (selectedMatriculaId) seleccionarAlumnoWorkspace(selectedMatriculaId);
            });
        })
        .catch(err => {
            console.error(err);
            showToast("Error al guardar calificaciones de grupo", "danger");
        });
    }

    // --- RECARGAR CACHÉ ---
    function recargarCalificacionesYActualizar(callback) {
        fetch('acciones/gestion_notas.php?action=get_notas')
        .then(res => res.json())
        .then(data => {
            dbData = data;
            if (callback) callback();
        });
    }

    cargarNotasProfesores();
    <?php endif; ?>
</script>

</body>
</html>


