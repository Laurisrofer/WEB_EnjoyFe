<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token'])) {
    header("Location: index.php");
    exit();
}

// --- CONFIGURACIÓN DEL HEADER ---
$pagina_id = 'horario';
$titulo_seccion = 'Mi horario';
$estilos_adicionales = '<link rel="stylesheet" href="recursos/horario.css">';

include 'componentes/header.php';
?>

<div class="contenedor-horario">
    <div class="tarjeta-horario">
        
        <!-- CABECERA DE LA VISTA -->
        <div class="horario-header">
            <div>
                <h2 class="horario-titulo" id="horario_grupo_nombre">Cargando Horario...</h2>
            </div>
            
            <div class="selector-container" id="admin_selector_container" style="display: none;">
                <label for="curso_select">Curso:</label>
                <select id="curso_select" class="form-control" style="width: auto; padding: 8px 15px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-color); cursor: pointer;" onchange="cargarHorario(this.value)">
                    <!-- Opciones cargadas por JS -->
                </select>
            </div>
        </div>

        <!-- REJILLA DEL HORARIO -->
        <div class="horario-grid" id="horario_grid_container">
            <!-- Rellenado dinámicamente por JavaScript -->
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

    // Función para obtener estilos de color
    function getSubjectStyle(match) {
        let bg, text, border;
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

        if (match.color && match.color !== '#3498db' && match.color !== '') {
            // Usa el color de la base de datos si fue personalizado
            bg = match.color;
            text = '#ffffff'; // Texto blanco para contrastar
            border = match.color;
        } else {
            // Fallback al color hash si no hay color personalizado
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

    // Función principal para cargar y pintar el horario
    function cargarHorario(idCurso = '') {
        const grid = document.getElementById('horario_grid_container');
        grid.innerHTML = '<div class="horario-cargando">Cargando datos del calendario...</div>';

        const url = idCurso ? `acciones/mi_horario.php?id_curso=${idCurso}` : 'acciones/mi_horario.php';

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
                document.getElementById('horario_grupo_nombre').innerHTML = `Horario - ${data.curso_nombre} <span style="font-size: 0.6em; color: var(--text-muted); margin-left: 15px; font-weight: normal; vertical-align: middle;">Tutor: <strong>${data.tutor_nombre || 'Sin asignar'}</strong></span>`;
            }

            // Si es administrador o profesor, pintar el selector de cursos (si no está pintado)
            if ((data.rol === 'admin' || data.rol === 'profesor') && data.cursos_disponibles && data.cursos_disponibles.length > 0) {
                const selectorContainer = document.getElementById('admin_selector_container');
                const select = document.getElementById('curso_select');
                
                if (selectorContainer.style.display === 'none') {
                    selectorContainer.style.display = 'flex';
                    select.innerHTML = data.cursos_disponibles.map(c => `
                        <option value="${c.id}" ${data.curso_nombre === c.nombre ? 'selected' : ''}>${c.nombre}</option>
                    `).join('');
                }
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
                                ? `<span class="asig-curso">${match.curso}</span>` 
                                : `<span class="asig-prof">${match.profesor}</span>`;

                            dayCell.innerHTML = `
                                <div class="asig-card" style="${style}">
                                    <div class="asig-name">${match.asignatura}</div>
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
