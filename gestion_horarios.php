<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token'])) {
    header("Location: index.php");
    exit();
}

// --- CONFIGURACIÓN DEL HEADER ---
$pagina_id = 'gestion_horarios';
$titulo_seccion = 'Gestión de Horarios';
$estilos_adicionales = '
<link rel="stylesheet" href="recursos/horario.css?v=' . time() . '">
<style>
.layout-horarios { display: flex; gap: 20px; align-items: flex-start; }
.panel-colores { flex: 0 0 250px; background: var(--card-bg); padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px var(--shadow-color); border: 1px solid var(--border-color); }
.panel-colores h3 { margin-top: 0; color: var(--text-color); font-size: 1.1em; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-bottom: 15px; }
.color-item { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 0.9em; color: var(--text-color); }
.color-picker { width: 30px; height: 30px; padding: 0; border: none; border-radius: 4px; cursor: pointer; }
.panel-calendario { flex: 1; }
@media (max-width: 768px) {
    .layout-horarios { flex-direction: column; }
    .panel-colores { width: 100%; flex: none; }
}
</style>
';

// Obtener cursos para el desplegable
$url_cursos = "http://127.0.0.1:5000/cursos"; 
$ch = curl_init($url_cursos);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $_SESSION['token'],
    'Content-Type: application/json'
]);
$res_cursos = curl_exec($ch);
$cursos = curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200 ? json_decode($res_cursos, true) : [];
curl_close($ch);

include 'componentes/header.php';
?>

<div class="contenedor-horario">
    
    <!-- CABECERA DE LA VISTA -->
    <div class="horario-header" style="background: var(--card-bg); padding: 15px 20px; border-radius: 8px; box-shadow: 0 2px 10px var(--shadow-color); border: 1px solid var(--border-color); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <h2 class="horario-titulo" id="horario_grupo_nombre" style="margin:0;">Cargando Horario...</h2>
        
        <div class="selector-container" id="admin_selector_container">
            <label for="curso_select">Curso:</label>
            <select id="curso_select" class="form-control" style="width: auto; padding: 8px 15px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-color); cursor: pointer;" onchange="cargarHorario(this.value)">
                <option value="">-- Seleccione un curso --</option>
                <?php foreach ($cursos as $c): ?>
                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="layout-horarios">
        
        <!-- PANEL DE COLORES -->
        <div class="panel-colores">
            <h3>Personalizar Colores</h3>
            <div id="lista_colores">
                <p style="color:var(--text-muted); font-size:0.9em; font-style:italic;">Selecciona un curso para ver sus asignaturas.</p>
            </div>
        </div>

        <!-- REJILLA DEL HORARIO -->
        <div class="panel-calendario">
            <div class="tarjeta-horario" style="margin-top:0;">
                <div class="horario-grid" id="horario_grid_container">
                    <!-- Rellenado dinámicamente por JavaScript -->
                </div>
            </div>
        </div>
        
    </div>
</div>

<script>
    // Configuración estática de los tramos horarios del centro educativo
    const slots = [
        { start: "08:00", end: "08:55" },
        { start: "08:55", end: "09:50" },
        { start: "09:50", end: "10:45" },
        { start: "10:45", end: "11:15", isRecreo: true },
        { start: "11:15", end: "12:10" },
        { start: "12:10", end: "13:05" },
        { start: "13:05", end: "14:00" }
    ];

    const diasNombres = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes"];

    let currentCursoId = '';

    // Función para obtener estilos de color
    function getSubjectStyle(match) {
        let bg, text, border;
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

        if (match.color && match.color !== '#3498db' && match.color !== '') {
            bg = match.color;
            text = '#ffffff';
            border = match.color;
        } else {
            let hash = 0;
            for (let i = 0; i < match.asignatura.length; i++) {
                hash = match.asignatura.charCodeAt(i) + ((hash << 5) - hash);
            }
            const hue = Math.abs(hash) % 360;
            bg = isDark ? `hsla(${hue}, 60%, 25%, 0.35)` : `hsla(${hue}, 85%, 96%, 1)`;
            text = isDark ? `hsl(${hue}, 85%, 85%)` : `hsl(${hue}, 70%, 30%)`;
            border = isDark ? `hsl(${hue}, 50%, 40%)` : `hsl(${hue}, 60%, 82%)`;
        }
        
        return `background-color: ${bg}; color: ${text}; border: 1px solid ${border};`;
    }

    // Función para guardar el color en base de datos
    function guardarColor(id_asignatura, color_hex) {
        fetch(`acciones/admin_editar_asignatura.php?id=${id_asignatura}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ color: color_hex })
        })
        .then(res => res.json())
        .then(data => {
            if(data.exito) {
                // Recargar el horario silenciosamente para aplicar colores
                cargarHorario(currentCursoId);
            } else {
                alert('Error al guardar el color: ' + data.mensaje);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error de conexión al guardar el color.');
        });
    }

    // Función principal para cargar y pintar el horario
    function cargarHorario(idCurso = '') {
        currentCursoId = idCurso;
        const grid = document.getElementById('horario_grid_container');
        const colorList = document.getElementById('lista_colores');
        
        if (!idCurso) {
            document.getElementById('horario_grupo_nombre').innerText = "Seleccione un curso";
            grid.innerHTML = '<div class="horario-cargando" style="color:var(--text-color);">Seleccione un curso en el desplegable superior para ver su horario y colores.</div>';
            colorList.innerHTML = '<p style="color:var(--text-muted); font-size:0.9em; font-style:italic;">Selecciona un curso para ver sus asignaturas.</p>';
            return;
        }

        grid.innerHTML = '<div class="horario-cargando">Cargando datos del calendario...</div>';

        const url = `acciones/mi_horario.php?id_curso=${idCurso}`;

        fetch(url)
        .then(response => {
            if (response.status === 200) return response.json();
            throw new Error('Error al cargar');
        })
        .then(data => {
            // Actualizar Cabecera
            if (data.rol === 'profesor') {
                document.getElementById('horario_grupo_nombre').innerHTML = "Mi horario docente";
            } else {
                document.getElementById('horario_grupo_nombre').innerHTML = `Horario - ${escapeHtml(data.curso_nombre)} <span style="font-size: 0.6em; color: var(--text-muted); margin-left: 15px; font-weight: normal; vertical-align: middle;">Tutor: <strong>${escapeHtml(data.tutor_nombre || 'Sin asignar')}</strong></span>`;
            }

            // Comenzar a pintar la rejilla
            grid.innerHTML = "";

            // 1. Pintar fila cabecera (esquinas + nombres de días)
            const emptyCorner = document.createElement('div');
            emptyCorner.className = "grid-cell grid-header";
            emptyCorner.innerText = "Hora";
            grid.appendChild(emptyCorner);

            diasNombres.forEach(dia => {
                const dayHeader = document.createElement('div');
                dayHeader.className = "grid-cell grid-header";
                dayHeader.innerText = dia;
                grid.appendChild(dayHeader);
            });

            // 2. Pintar las filas tramo a tramo
            slots.forEach((slot, index) => {
                if (slot.isRecreo) {
                    // Fila especial de recreo
                    const recreoCell = document.createElement('div');
                    recreoCell.className = "recreo-row";
                    recreoCell.innerHTML = `RECREO (${slot.start} - ${slot.end})`;
                    grid.appendChild(recreoCell);
                } else {
                    // Celda de hora izquierda
                    const timeCell = document.createElement('div');
                    timeCell.className = "grid-cell grid-time";
                    timeCell.innerHTML = `
                        <span class="time-slot">${slot.start}</span>
                        <span class="time-range">${slot.end}</span>
                    `;
                    grid.appendChild(timeCell);

                    // Celdas para cada día de la semana (1 = Lunes a 5 = Viernes)
                    for (let day = 1; day <= 5; day++) {
                        // Buscar si coincide alguna asignatura en este día y tramo
                        const match = data.horarios.find(h => h.dia_semana === day && h.hora_inicio === slot.start);
                        
                        const dayCell = document.createElement('div');
                        dayCell.className = "grid-cell";

                        // Si es el último tramo del horario, marcamos clase para quitar borde de abajo
                        if (index === slots.length - 1) {
                            dayCell.classList.add("border-bottom-none");
                        }

                        if (match) {
                            const style = getSubjectStyle(match);
                            // Si es profesor, además del nombre de asignatura enseñamos a qué curso pertenece
                            const detailSubtext = data.rol === 'profesor' 
                                ? `<span class="asig-curso">${escapeHtml(match.curso)}</span>` 
                                : `<span class="asig-prof">${escapeHtml(match.profesor)}</span>`;

                            dayCell.innerHTML = `
                                <div class="asig-card" style="${style}">
                                    <div class="asig-name">${escapeHtml(match.asignatura)}</div>
                                    ${detailSubtext}
                                </div>
                            `;
                        } else {
                            dayCell.innerHTML = "";
                        }
                        grid.appendChild(dayCell);
                    }
                }
            });

            // Extraer asignaturas únicas y renderizar panel de colores
            const colorList = document.getElementById('lista_colores');
            if (!data.horarios || data.horarios.length === 0) {
                colorList.innerHTML = '<p style="color:var(--text-muted); font-size:0.9em; font-style:italic;">No hay asignaturas en este horario.</p>';
            } else {
                const asignaturasUnicas = {};
                data.horarios.forEach(h => {
                    if (!asignaturasUnicas[h.id_asignatura]) {
                        asignaturasUnicas[h.id_asignatura] = {
                            id: h.id_asignatura,
                            nombre: h.asignatura,
                            color: h.color || '#3498db'
                        };
                    }
                });

                const palette = [
                    '#ff9ff3', '#feca57', '#ff6b6b', '#48dbfb', '#1dd1a1',
                    '#f368e0', '#ff9f43', '#ee5253', '#0abde3', '#10ac84',
                    '#00d2d3', '#54a0ff', '#5f27cd', '#c8d6e5', '#576574',
                    '#01a3a4', '#2e86de', '#341f97', '#8395a7', '#222f3e'
                ];

                colorList.innerHTML = Object.values(asignaturasUnicas).map(a => {
                    const paletteHtml = palette.map(c => `
                        <div style="width:18px; height:18px; background-color:${c}; cursor:pointer; border-radius:3px; border: 2px solid ${a.color === c ? '#333' : 'transparent'}; box-sizing: border-box; transition: transform 0.1s;" 
                             onclick="guardarColor(${a.id}, '${c}')" 
                             onmouseover="this.style.transform='scale(1.2)'" 
                             onmouseout="this.style.transform='scale(1)'" 
                             title="Cambiar color"></div>
                    `).join('');

                    return `
                    <div class="color-item" style="display:flex; flex-direction:column; align-items:flex-start; margin-bottom: 25px; border-bottom:1px solid var(--border-color); padding-bottom:15px; width: 100%;">
                        <span style="font-weight:bold; margin-bottom:10px; width:100%; word-wrap:break-word;">${escapeHtml(a.nombre)}</span>
                        <div style="display:flex; flex-wrap:wrap; gap:6px; width:100%;">
                            ${paletteHtml}
                        </div>
                    </div>
                `}).join('');
            }

        })
        .catch(err => {
            console.error(err);
            grid.innerHTML = `
                <div class="horario-error">
                    <h3>⚠️ Error de conexión</h3>
                    <p>No se pudo cargar la información del horario. Por favor, verifica que el servidor esté activo.</p>
                </div>
            `;
        });
    }

    // Arrancar la primera carga de la página
    document.addEventListener("DOMContentLoaded", () => {
        cargarHorario();
    });
</script>

<?php include 'componentes/footer.php'; ?>
</body>
</html>
