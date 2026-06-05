<?php
session_start();

if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token'])) {
    header("Location: index.php");
    exit();
}

$pagina_id = 'estadisticas';
$titulo_seccion = 'Estadísticas';
$rol_usuario = $_SESSION['rol'];

include 'componentes/header.php';
?>

<div class="contenedor-datos">
    <?php if ($rol_usuario === 'admin'): ?>
        <div class="card-resumen">
            <h2>Salud del centro</h2>
            <p style="color:var(--text-muted);">Métricas globales de la plataforma.</p>
        </div>
        <div id="loading_stats" class="loading-indicator">Cargando métricas globales...</div>
        
        <div id="admin_stats_container" style="display: none; margin-top: 25px;">
            <div class="roscos-grid">
                <div class="rosco-card">
                    <h3>Usuarios</h3>
                    <div style="font-size: 2em; text-align: center; color: var(--primary-color); margin: 20px 0;">
                        <span id="admin_alumnos">0</span> <span style="font-size: 0.5em; color: var(--text-muted);">Alumnos</span>
                    </div>
                    <div style="font-size: 2em; text-align: center; color: var(--primary-color); margin: 20px 0;">
                        <span id="admin_profes">0</span> <span style="font-size: 0.5em; color: var(--text-muted);">Profesores</span>
                    </div>
                </div>
                
                <div class="rosco-card">
                    <h3>Cursos Activos</h3>
                    <div style="font-size: 3em; text-align: center; color: var(--primary-color); margin: 30px 0;">
                        <span id="admin_cursos">0</span>
                    </div>
                </div>
            </div>
            
            <div class="charts-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 40px;">
                <div class="chart-container" style="background: var(--card-bg); padding: 20px; border-radius: 8px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px var(--shadow-color);">
                    <h3 style="text-align:center; margin-top:0;">Distribución de Usuarios</h3>
                    <canvas id="chartUsuarios"></canvas>
                </div>
                
                <div class="chart-container" style="background: var(--card-bg); padding: 20px; border-radius: 8px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px var(--shadow-color);">
                    <h3 style="text-align:center; margin-top:0;">Alumnos por Curso</h3>
                    <canvas id="chartAlumnos"></canvas>
                </div>
                
                <div class="chart-container" style="background: var(--card-bg); padding: 20px; border-radius: 8px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px var(--shadow-color);">
                    <h3 style="text-align:center; margin-top:0;">Asistencia Media (%)</h3>
                    <canvas id="chartAsistencia"></canvas>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card-resumen">
            <h2>Estadísticas académicas</h2>
            <p style="color:var(--text-muted);">Selecciona una asignatura para ver tu rendimiento.</p>
        </div>

        <div id="loading_stats" class="loading-indicator">Cargando estadísticas...</div>
    <?php endif; ?>

    <?php if ($rol_usuario === 'profesor'): ?>
        <script>const esProfesor = true;</script>
    <?php else: ?>
        <script>const esProfesor = false;</script>
    <?php endif; ?>

    <div id="stats_container" style="display: none;">

        <!-- SELECTOR DE CURSO Y ASIGNATURA -->
        <div class="card-resumen" style="margin-bottom: 25px; display: flex; gap: 20px; flex-wrap: wrap; align-items: center;">
            <div id="curso_selector_wrapper" style="display:none;">
                <label for="stats_curso_select" style="font-weight:600; margin-right:10px; color:var(--text-color);">Curso:</label>
                <select id="stats_curso_select" onchange="filtrarAsignaturasPorCurso()" style="padding:10px 15px; border-radius:8px; border:1px solid var(--border-color); background:var(--input-bg); color:var(--text-color); font-size:1em; min-width:200px; cursor:pointer;">
                    <option value="">-- Todos los cursos --</option>
                </select>
            </div>
            
            <div>
                <label for="stats_asig_select" style="font-weight:600; margin-right:10px; color:var(--text-color);">Asignatura:</label>
                <select id="stats_asig_select" onchange="actualizarRoscos()" style="padding:10px 15px; border-radius:8px; border:1px solid var(--border-color); background:var(--input-bg); color:var(--text-color); font-size:1em; min-width:250px; cursor:pointer;">
                    <option value="">-- Selecciona asignatura --</option>
                </select>
            </div>
        </div>

        <!-- ROSCOS -->
        <div id="roscos_wrapper" style="display:none;">
            <div class="roscos-grid">

                <!-- ROSCO NOTAS -->
                <div class="rosco-card">
                    <h3>Nota media</h3>
                    <div class="rosco-container">
                        <div class="rosco" id="rosco_notas">
                            <div class="rosco-inner">
                                <span class="rosco-valor" id="rosco_notas_valor">-</span>
                                <span class="rosco-label">sobre 10</span>
                            </div>
                        </div>
                    </div>
                    <div class="rosco-detalle" id="rosco_notas_detalle"></div>
                </div>

                <!-- ROSCO ASISTENCIA -->
                <div class="rosco-card">
                    <h3 id="asistencia_titulo">Asistencia</h3>
                    <div class="rosco-container">
                        <div class="rosco" id="rosco_asistencia">
                            <div class="rosco-inner">
                                <span class="rosco-valor" id="rosco_asistencia_valor">-</span>
                                <span class="rosco-label" id="asistencia_label">asistencia</span>
                            </div>
                        </div>
                    </div>
                    <div class="rosco-detalle" id="rosco_asistencia_detalle"></div>
                </div>

            </div>

            <!-- Gráfico Dinámico (Profesor/Alumno) -->
            <div id="chart_detalles_wrapper" class="card-resumen" style="margin-top: 30px; display: none;">
                <h3 id="chart_detalles_titulo" style="text-align:center;">Detalle de Notas</h3>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="chartDetalles"></canvas>
                </div>
            </div>
        </div>

        <!-- MENSAJE INICIAL -->
        <div id="stats_placeholder" class="card-resumen" style="text-align:center; padding:40px; color:var(--text-muted);">
            <p style="font-size:1.1em;">Elige una asignatura del desplegable para ver tus estadísticas.</p>
        </div>

    </div>
</div>

<style>
    .contenedor-datos {
        padding: 30px;
    }
    .card-resumen {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 25px;
    }
    .card-resumen h2 {
        margin: 0 0 10px 0;
        font-size: 1.5em;
        color: var(--text-color);
    }
    .roscos-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }
    @media (max-width: 700px) {
        .roscos-grid {
            grid-template-columns: 1fr;
        }
    }

    .rosco-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 30px 20px;
        text-align: center;
    }
    .rosco-card h3 {
        margin: 0 0 20px 0;
        font-size: 1.1em;
        color: var(--text-color);
    }

    .rosco-container {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .rosco {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: conic-gradient(#e0e0e0 0% 100%);
        transition: background 0.6s ease;
        position: relative;
    }

    .rosco-inner {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: var(--card-bg);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }

    .rosco-valor {
        font-size: 2.4em;
        font-weight: 800;
        line-height: 1;
        color: var(--text-color);
    }
    .rosco-label {
        font-size: 0.8em;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .rosco-detalle {
        font-size: 0.88em;
        color: var(--text-muted);
        line-height: 1.7;
    }
    .rosco-detalle span.tag-eval {
        display: inline-block;
        background: var(--input-bg);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 3px 10px;
        margin: 3px 4px;
        font-weight: 600;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let datosNotas = null;
    let datosAsistencias = null;
    let chartInstancia = null;

    function extraerEvalStat(actividad) {
        if (actividad.startsWith('[1] ')) return '1';
        if (actividad.startsWith('[2] ')) return '2';
        if (actividad.startsWith('[3] ')) return '3';
        if (actividad.startsWith('[1ª Eval] ')) return '1';
        if (actividad.startsWith('[2ª Eval] ')) return '2';
        if (actividad.startsWith('[3ª Eval] ')) return '3';
        const match = actividad.match(/^\[(\d+)[^\]]*\]\s*/);
        if (match) return match[1];
        return '-';
    }

    function getColorNota(nota) {
        if (nota < 5) return 'var(--danger-color)';
        if (nota < 7) return '#e6a817';
        return 'var(--success-color)';
    }

    function getColorAsistencia(pct) {
        if (pct < 75) return 'var(--danger-color)';
        if (pct < 90) return '#e6a817';
        return 'var(--success-color)';
    }

    function setRosco(elementId, porcentaje, color) {
        const el = document.getElementById(elementId);
        const bg = getComputedStyle(document.documentElement).getPropertyValue('--border-color').trim() || '#e0e0e0';
        el.style.background = `conic-gradient(${color} 0% ${porcentaje}%, ${bg} ${porcentaje}% 100%)`;
    }

    const esAdmin = <?php echo ($rol_usuario === 'admin') ? 'true' : 'false'; ?>;

    function cargarEstadisticas() {
        if (esAdmin) {
            fetch('acciones/admin_stats.php')
            .then(r => r.ok ? r.json() : null)
            .then(data => {
                document.getElementById('loading_stats').style.display = 'none';
                if (!data) return;
                
                document.getElementById('admin_stats_container').style.display = 'block';
                document.getElementById('admin_alumnos').innerText = data.total_alumnos || 0;
                document.getElementById('admin_profes').innerText = data.total_profesores || 0;
                document.getElementById('admin_cursos').innerText = data.total_cursos || 0;
                
                // Gráfico 1: Usuarios (Donut)
                new Chart(document.getElementById('chartUsuarios'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Alumnos', 'Profesores'],
                        datasets: [{
                            data: [data.total_alumnos || 0, data.total_profesores || 0],
                            backgroundColor: ['#3498db', '#e74c3c'],
                            borderWidth: 0
                        }]
                    },
                    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') } } } }
                });

                // Gráfico 2: Alumnos por curso (Barras)
                if (data.alumnos_por_curso) {
                    new Chart(document.getElementById('chartAlumnos'), {
                        type: 'bar',
                        data: {
                            labels: data.alumnos_por_curso.map(c => c.curso),
                            datasets: [{
                                label: 'Alumnos Matriculados',
                                data: data.alumnos_por_curso.map(c => c.cantidad),
                                backgroundColor: 'rgba(52, 152, 219, 0.7)',
                                borderColor: '#3498db',
                                borderWidth: 1
                            }]
                        },
                        options: { 
                            responsive: true, 
                            scales: { 
                                y: { beginAtZero: true, ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color'), stepSize: 1 } },
                                x: { ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') } }
                            },
                            plugins: { legend: { display: false } }
                        }
                    });
                }

                // Gráfico 3: Asistencia Media (Barras horizontales)
                if (data.asistencia_por_curso) {
                    new Chart(document.getElementById('chartAsistencia'), {
                        type: 'bar',
                        data: {
                            labels: data.asistencia_por_curso.map(c => c.curso),
                            datasets: [{
                                label: '% Asistencia Media',
                                data: data.asistencia_por_curso.map(c => c.porcentaje),
                                backgroundColor: data.asistencia_por_curso.map(c => getColorAsistencia(c.porcentaje)),
                                borderWidth: 0
                            }]
                        },
                        options: { 
                            responsive: true, 
                            indexAxis: 'y',
                            scales: { 
                                x: { beginAtZero: true, max: 100, ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') } },
                                y: { ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') } }
                            },
                            plugins: { legend: { display: false } }
                        }
                    });
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('loading_stats').innerHTML = '<div style="color:var(--danger-color)">Error al cargar estadísticas.</div>';
            });
        } else if (esProfesor) {
            fetch('acciones/estadisticas_profesor.php')
            .then(r => r.ok ? r.json() : null)
            .then(data => {
                datosNotas = data; // Usaremos esto para guardar la info del profesor
                
                document.getElementById('loading_stats').style.display = 'none';
                document.getElementById('stats_container').style.display = 'block';

                const select = document.getElementById('stats_asig_select');
                if (data && data.length > 0) {
                    document.getElementById('curso_selector_wrapper').style.display = 'block';
                    const cursoSelect = document.getElementById('stats_curso_select');
                    const cursosMap = new Map();
                    data.forEach(a => {
                        if (a.id_curso && a.nombre_curso) {
                            cursosMap.set(a.id_curso, a.nombre_curso);
                        }
                    });
                    
                    cursosMap.forEach((nombre, id) => {
                        const opt = document.createElement('option');
                        opt.value = id;
                        opt.textContent = nombre;
                        cursoSelect.appendChild(opt);
                    });
                    
                    filtrarAsignaturasPorCurso();
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('loading_stats').innerHTML = '<div style="color:var(--danger-color)">Error al cargar estadísticas.</div>';
            });
        } else {
            Promise.all([
                fetch('acciones/gestion_notas.php?action=get_mis_notas').then(r => r.ok ? r.json() : null),
                fetch('acciones/gestion_asistencias.php?action=get_mis_asistencias').then(r => r.ok ? r.json() : null)
            ])
            .then(([notas, asistencias]) => {
                datosNotas = notas;
                datosAsistencias = asistencias;

                document.getElementById('loading_stats').style.display = 'none';
                document.getElementById('stats_container').style.display = 'block';

                // Poblar select con asignaturas desde notas
                const select = document.getElementById('stats_asig_select');
                if (notas && notas.asignaturas) {
                    notas.asignaturas.forEach(a => {
                        const opt = document.createElement('option');
                        opt.value = a.asignatura;
                        opt.textContent = a.asignatura;
                        select.appendChild(opt);
                    });
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('loading_stats').innerHTML = '<div style="color:var(--danger-color)">Error al cargar estadísticas.</div>';
            });
        }
    }

    function filtrarAsignaturasPorCurso() {
        if (!esProfesor) return;
        
        const idCurso = document.getElementById('stats_curso_select').value;
        const selectAsig = document.getElementById('stats_asig_select');
        selectAsig.innerHTML = '<option value="">-- Selecciona asignatura --</option>';
        
        if (datosNotas) {
            datosNotas.forEach(a => {
                if (!idCurso || String(a.id_curso) === String(idCurso)) {
                    const opt = document.createElement('option');
                    opt.value = a.id_asignatura;
                    opt.textContent = a.nombre_asignatura;
                    selectAsig.appendChild(opt);
                }
            });
        }
        actualizarRoscos();
    }

    function actualizarRoscos() {
        const asigVal = document.getElementById('stats_asig_select').value;

        if (!asigVal) {
            document.getElementById('roscos_wrapper').style.display = 'none';
            document.getElementById('stats_placeholder').style.display = 'block';
            return;
        }

        document.getElementById('stats_placeholder').style.display = 'none';
        document.getElementById('roscos_wrapper').style.display = 'block';

        if (esProfesor) {
            // asigVal es id_asignatura
            const asig = datosNotas.find(a => String(a.id_asignatura) === String(asigVal));
            if (!asig) return;

            // Rosco Notas: Nota media de la clase
            const mediaClase = asig.nota_media || 0;
            const colorMedia = getColorNota(mediaClase);
            const pctMedia = (mediaClase / 10) * 100;
            
            setRosco('rosco_notas', pctMedia, colorMedia);
            document.getElementById('rosco_notas_valor').textContent = asig.total_alumnos > 0 ? mediaClase.toFixed(1) : '-';
            document.getElementById('rosco_notas_valor').style.color = colorMedia;
            document.getElementById('rosco_notas').parentElement.previousElementSibling.textContent = 'Nota media de la clase';
            document.getElementById('rosco_notas').querySelector('.rosco-label').textContent = 'sobre 10';
            document.getElementById('rosco_notas_detalle').innerHTML = `Alumnos evaluados: ${asig.total_alumnos} <br> Aprobados: ${asig.porcentaje_aprobados}%`;

            // Rosco Asistencia: % de asistencia media de la clase
            document.getElementById('asistencia_titulo').textContent = 'Asistencia global';
            document.getElementById('asistencia_label').textContent = 'asistencia';
            
            const pctAsist = asig.total_alumnos > 0 ? asig.porcentaje_asistencia : 0;
            const colorAsist = getColorAsistencia(pctAsist);
            
            setRosco('rosco_asistencia', pctAsist, colorAsist);
            document.getElementById('rosco_asistencia_valor').textContent = asig.total_alumnos > 0 ? pctAsist + '%' : '-';
            document.getElementById('rosco_asistencia_valor').style.color = colorAsist;
            document.getElementById('rosco_asistencia_detalle').innerHTML = `Faltas de la clase: ${asig.total_faltas_registradas} <br> Retrasos: ${asig.total_retrasos_registrados || 0}`;
            
            // Render Chart
            document.getElementById('chart_detalles_wrapper').style.display = 'block';
            document.getElementById('chart_detalles_titulo').innerText = 'Distribución de Notas';
            if (chartInstancia) chartInstancia.destroy();
            
            if (asig.distribucion_notas) {
                const dist = asig.distribucion_notas;
                chartInstancia = new Chart(document.getElementById('chartDetalles'), {
                    type: 'bar',
                    data: {
                        labels: ['Suspenso', 'Suficiente', 'Bien', 'Notable', 'Sobresaliente'],
                        datasets: [{
                            label: 'Nº Alumnos',
                            data: [dist.Suspenso, dist.Suficiente, dist.Bien, dist.Notable, dist.Sobresaliente],
                            backgroundColor: [
                                'rgba(231, 76, 60, 0.7)',
                                'rgba(241, 196, 15, 0.7)',
                                'rgba(52, 152, 219, 0.7)',
                                'rgba(46, 204, 113, 0.7)',
                                'rgba(39, 174, 96, 0.9)'
                            ],
                            borderColor: [
                                '#e74c3c', '#f1c40f', '#3498db', '#2ecc71', '#27ae60'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: { 
                        responsive: true, maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true, ticks: { stepSize: 1, color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') } }, x: { ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') } } },
                        plugins: { legend: { display: false } }
                    }
                });
            }
            
        } else {
            // Lógica existente de Alumno
            const asigNombre = asigVal;
            // --- ROSCO NOTAS ---
            let media = 0;
            let detalleHtml = '';
            if (datosNotas && datosNotas.asignaturas) {
                const asig = datosNotas.asignaturas.find(a => a.asignatura === asigNombre);
                if (asig && asig.calificaciones && asig.calificaciones.length > 0) {
                    const notas = asig.calificaciones.map(c => c.nota).filter(n => n !== null);
                    media = notas.reduce((a, b) => a + b, 0) / notas.length;

                    // Medias por evaluación
                    let tags = [];
                    for (let ev = 1; ev <= 3; ev++) {
                        const notasEv = asig.calificaciones
                            .filter(c => extraerEvalStat(c.actividad) === String(ev))
                            .map(c => c.nota)
                            .filter(n => n !== null);
                        if (notasEv.length > 0) {
                            const mediaEv = (notasEv.reduce((a, b) => a + b, 0) / notasEv.length).toFixed(1);
                            const colorEv = getColorNota(parseFloat(mediaEv));
                            tags.push(`<span class="tag-eval" style="color:${colorEv};">Eval ${ev}: ${mediaEv}</span>`);
                        }
                    }
                    detalleHtml = tags.join(' ');
                }
            }

            const colorNota = getColorNota(media);
            const pctNota = (media / 10) * 100;
            setRosco('rosco_notas', pctNota, colorNota);
            document.getElementById('rosco_notas_valor').textContent = media.toFixed(1);
            document.getElementById('rosco_notas_valor').style.color = colorNota;
            document.getElementById('rosco_notas_detalle').innerHTML = detalleHtml || '<span style="color:var(--text-muted);">Sin notas</span>';
            document.getElementById('rosco_notas').previousElementSibling.textContent = 'Nota media'; // Titulo original
            document.getElementById('rosco_notas').querySelector('.rosco-label').textContent = 'sobre 10';

            // --- ROSCO ASISTENCIA ---
            let faltas = 0, retrasos = 0, justificadas = 0;
            if (datosAsistencias && datosAsistencias.asistencias) {
                datosAsistencias.asistencias.forEach(a => {
                    if (a.asignatura === asigNombre) {
                        if (a.tipo === 'Falta' || a.tipo === 'falta') faltas++;
                        if (a.tipo === 'Retraso' || a.tipo === 'retraso') retrasos++;
                        if (a.justificante_estado === 'aprobado' || a.justificante_estado === 'Aprobado') justificadas++;
                    }
                });
            }

            const clasesEstimadas = 40;
            const asistidas = Math.max(0, clasesEstimadas - faltas);
            const pctAsist = Math.round((asistidas / clasesEstimadas) * 100);
            const colorAsist = getColorAsistencia(pctAsist);

            document.getElementById('asistencia_titulo').textContent = 'Asistencia';
            document.getElementById('asistencia_label').textContent = 'asistencia';

            setRosco('rosco_asistencia', pctAsist, colorAsist);
            document.getElementById('rosco_asistencia_valor').textContent = pctAsist + '%';
            document.getElementById('rosco_asistencia_valor').style.color = colorAsist;

            let detParts = [];
            if (faltas > 0) detParts.push(faltas + (faltas === 1 ? ' falta' : ' faltas'));
            if (retrasos > 0) detParts.push(retrasos + (retrasos === 1 ? ' retraso' : ' retrasos'));
            if (justificadas > 0) detParts.push(justificadas + ' justif.');
            document.getElementById('rosco_asistencia_detalle').innerHTML = detParts.length > 0
                ? detParts.join(' · ')
                : '<span style="color:var(--success-color); font-weight:600;">Sin incidencias</span>';
                
            // Render Chart (Evolución de notas)
            document.getElementById('chart_detalles_wrapper').style.display = 'block';
            document.getElementById('chart_detalles_titulo').innerText = 'Evolución de Notas (por Evaluación)';
            if (chartInstancia) chartInstancia.destroy();
            
            // Recoger datos de evaluación extraídos previamente (en `tags`)
            let evalData = [null, null, null];
            if (datosNotas && datosNotas.asignaturas) {
                const asig = datosNotas.asignaturas.find(a => a.asignatura === asigNombre);
                if (asig && asig.calificaciones) {
                    for (let ev = 1; ev <= 3; ev++) {
                        const notasEv = asig.calificaciones
                            .filter(c => extraerEvalStat(c.actividad) === String(ev))
                            .map(c => c.nota)
                            .filter(n => n !== null);
                        if (notasEv.length > 0) {
                            evalData[ev - 1] = (notasEv.reduce((a, b) => a + b, 0) / notasEv.length).toFixed(2);
                        }
                    }
                }
            }
            
            chartInstancia = new Chart(document.getElementById('chartDetalles'), {
                type: 'line',
                data: {
                    labels: ['1ª Evaluación', '2ª Evaluación', '3ª Evaluación'],
                    datasets: [{
                        label: 'Nota Media',
                        data: evalData,
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.2)',
                        pointBackgroundColor: '#3498db',
                        pointRadius: 6,
                        borderWidth: 3,
                        tension: 0.3,
                        spanGaps: true
                    }]
                },
                options: { 
                    responsive: true, maintainAspectRatio: false,
                    scales: { 
                        y: { beginAtZero: true, max: 10, ticks: { stepSize: 1, color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') } },
                        x: { ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }
    }

    cargarEstadisticas();
</script>

</div>
</div>
<?php include 'componentes/footer.php'; ?>
</body>
</html>
