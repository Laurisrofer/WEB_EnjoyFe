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
    <div class="card-resumen">
        <h2>Estadísticas académicas</h2>
        <p style="color:var(--text-muted);">Selecciona una asignatura para ver tu rendimiento.</p>
    </div>

    <div id="loading_stats" class="loading-indicator">Cargando estadísticas...</div>

    <div id="stats_container" style="display: none;">

        <!-- SELECTOR DE ASIGNATURA -->
        <div class="card-resumen" style="margin-bottom: 25px;">
            <label for="stats_asig_select" style="font-weight:600; margin-right:10px; color:var(--text-color);">Asignatura:</label>
            <select id="stats_asig_select" onchange="actualizarRoscos()" style="padding:10px 15px; border-radius:8px; border:1px solid var(--border-color); background:var(--input-bg); color:var(--text-color); font-size:1em; min-width:250px; cursor:pointer;">
                <option value="">-- Selecciona --</option>
            </select>
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
                    <h3>Asistencia</h3>
                    <div class="rosco-container">
                        <div class="rosco" id="rosco_asistencia">
                            <div class="rosco-inner">
                                <span class="rosco-valor" id="rosco_asistencia_valor">-</span>
                                <span class="rosco-label">asistencia</span>
                            </div>
                        </div>
                    </div>
                    <div class="rosco-detalle" id="rosco_asistencia_detalle"></div>
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

<script>
    let datosNotas = null;
    let datosAsistencias = null;

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

    function cargarEstadisticas() {
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

    function actualizarRoscos() {
        const asigNombre = document.getElementById('stats_asig_select').value;

        if (!asigNombre) {
            document.getElementById('roscos_wrapper').style.display = 'none';
            document.getElementById('stats_placeholder').style.display = 'block';
            return;
        }

        document.getElementById('stats_placeholder').style.display = 'none';
        document.getElementById('roscos_wrapper').style.display = 'block';

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
    }

    cargarEstadisticas();
</script>

</div>
</div>
</body>
</html>
