<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token'])) {
    header("Location: index.php");
    exit();
}

$url = "http://127.0.0.1:5000/academico/dashboard-info"; 
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $_SESSION['token'],
    'Content-Type: application/json'
]);

$respuesta = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$dashboard_data = [];
$anuncios = [];
$tareas = [];

if ($http_code == 200) {
    $dashboard_data = json_decode($respuesta, true);
    if (isset($dashboard_data['eventos'])) {
        foreach ($dashboard_data['eventos'] as $ev) {
            if ($ev['tipo'] == 'anuncio') {
                $anuncios[] = $ev;
            } else {
                $tareas[] = $ev;
            }
        }
    }
}

// --- CONFIGURACIÓN DEL HEADER ---
$pagina_id = 'inicio';
$titulo_seccion = 'Panel de control';
$estilos_adicionales = '<link rel="stylesheet" href="recursos/dashboard.css">';

// --- CAMBIO: Ruta actualizada ---
include 'componentes/header.php'; 
?>

        <div class="contenedor-datos">
            <?php if ($http_code != 200 || empty($dashboard_data)): ?>
                <div class="tarjeta tarjeta-error">
                    <h3>⚠️ Error de conexión</h3>
                    <p>No se ha podido cargar la información del panel de control.</p>
                </div>
            <?php else: ?>
                <div class="banner-academico">
                    <div>
                        <h2>¡Hola, <?php echo htmlspecialchars($dashboard_data['nombre']); ?>!</h2>
                        <p>Estás matriculado/a en: <strong><?php echo htmlspecialchars($dashboard_data['curso']); ?></strong></p>
                    </div>
                    <div class="tutor-badge">
                        Tutor: <?php echo htmlspecialchars($dashboard_data['tutor']); ?>
                    </div>
                </div>
                <div class="dashboard-grid">
                    <div class="col-izq">
                        <div class="tarjeta">
                            <h3>📌 Tablón de anuncios</h3>
                            <div class="tarjeta-scroll">
                                <?php if (empty($anuncios)): ?>
                                    <p class="estado-vacio">No hay avisos recientes del centro.</p>
                                <?php else: ?>
                                    <?php foreach ($anuncios as $anuncio): ?>
                                        <div class="evento-item anuncio" style="cursor: pointer;" 
                                             data-id="<?php echo $anuncio['id']; ?>"
                                             data-titulo="<?php echo htmlspecialchars($anuncio['titulo'], ENT_QUOTES, 'UTF-8'); ?>"
                                             data-descripcion="<?php echo htmlspecialchars($anuncio['descripcion'], ENT_QUOTES, 'UTF-8'); ?>"
                                             data-fecha="<?php echo htmlspecialchars($anuncio['fecha']); ?>"
                                             onclick="verDetalleAnuncioDashboard(this)">
                                            <div>
                                                <div class="evento-fecha"><?php echo htmlspecialchars($anuncio['fecha']); ?></div>
                                                <div class="evento-titulo"><?php echo htmlspecialchars($anuncio['titulo']); ?></div>
                                            </div>
                                             <div class="anuncio-estado" id="anuncio_estado_<?php echo $anuncio['id']; ?>"></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="tarjeta">
                            <h3>📅 Próximos eventos y entregas</h3>
                            <div class="tarjeta-scroll">
                                <?php if (empty($tareas)): ?>
                                    <p class="estado-vacio">No tienes entregas ni exámenes próximos. Pulsa en el calendario para añadir uno.</p>
                                <?php else: ?>
                                    <?php foreach ($tareas as $tarea): ?>
                                        <div class="evento-item <?php echo htmlspecialchars($tarea['tipo']); ?>">
                                            <div>
                                                <div class="evento-fecha"><?php echo htmlspecialchars($tarea['fecha']); ?> - <?php echo htmlspecialchars($tarea['hora']); ?></div>
                                                <div class="evento-titulo"><?php echo htmlspecialchars($tarea['titulo']); ?></div>
                                            </div>
                                            <div>
                                                 <button class="btn-icon" onclick="preparar_edicion(<?php echo $tarea['id']; ?>, '<?php echo addslashes($tarea['titulo']); ?>', '<?php echo $tarea['fecha']; ?>', '<?php echo $tarea['hora']; ?>', '<?php echo $tarea['tipo']; ?>')">✏️</button>
                                                 <a href="#" onclick="event.preventDefault(); mostrarConfirmacionGlobal('Eliminar evento', '¿Estás seguro de que deseas eliminar este evento de forma permanente?', () => { window.location.href = 'acciones/borrar_evento.php?id=<?php echo $tarea['id']; ?>'; })" class="link-icon" style="text-decoration:none; margin-left:10px;" title="Eliminar evento">❌</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-der">
                        <div class="tarjeta">
                            <h3>📆 Calendario mensual</h3>
                             <p class="calendar-help">Haz clic en un día para añadir un evento</p>
                            <div class="calendar-header">
                                <button class="calendar-btn" onclick="cambiar_mes(-1)">&#10094;</button>
                                <span id="mes-anio"></span>
                                <button class="calendar-btn" onclick="cambiar_mes(1)">&#10095;</button>
                            </div>
                            <div class="calendar-grid">
                                <div class="calendar-day-header">L</div><div class="calendar-day-header">M</div><div class="calendar-day-header">X</div><div class="calendar-day-header">J</div><div class="calendar-day-header">V</div><div class="calendar-day-header">S</div><div class="calendar-day-header">D</div>
                            </div>
                            <div id="calendar-body" class="calendar-grid"></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div> <div id="modalEvento" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Evento</h3>
            </div>
            <form id="formEvento" onsubmit="guardar_evento(event)">
                <input type="hidden" id="editIdInput" name="id">
                <input type="hidden" id="fechaInput" name="fecha">
                <div class="form-group">
                    <label>Título</label>
                    <input type="text" id="tituloInput" required placeholder="Título del evento">
                </div>
                <div class="form-group">
                    <label>Hora</label>
                    <input type="time" id="horaInput" required>
                </div>
                <div class="form-group">
                    <label>Tipo</label>
                    <select id="tipoInput">
                        <option value="personal">Personal</option>
                        <option value="entrega">Entrega</option>
                        <option value="examen">Examen</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancelar" onclick="cerrar_modal()">Cancelar</button>
                    <button type="submit" class="btn-guardar">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        let fecha_actual = new Date();
        let dia_actual = fecha_actual.getDate();
        let mes_actual = fecha_actual.getMonth();
        let anio_actual = fecha_actual.getFullYear();
        const modal = document.getElementById("modalEvento");
        const fecha_input = document.getElementById("fechaInput");

        function generar_calendario(mes, anio) {
            const body_calendario = document.getElementById("calendar-body");
            const texto_mes_anio = document.getElementById("mes-anio");
            body_calendario.innerHTML = "";
            texto_mes_anio.innerText = meses[mes] + " " + anio;
            let primer_dia = new Date(anio, mes, 1).getDay();
            primer_dia = primer_dia === 0 ? 6 : primer_dia - 1; 
            const dias_en_mes = new Date(anio, mes + 1, 0).getDate();
            for (let i = 0; i < primer_dia; i++) {
                const celda = document.createElement("div");
                celda.classList.add("calendar-day", "empty");
                body_calendario.appendChild(celda);
            }
            for (let i = 1; i <= dias_en_mes; i++) {
                const celda = document.createElement("div");
                celda.classList.add("calendar-day", "filled");
                celda.innerText = i;
                celda.onclick = () => {
                    document.getElementById("editIdInput").value = "";
                    document.getElementById("formEvento").reset();
                    const mes_format = (mes + 1).toString().padStart(2, '0');
                    const dia_str = i.toString().padStart(2, '0');
                    fecha_input.value = `${anio}-${mes_format}-${dia_str}`;
                    modal.style.display = "flex";
                };
                body_calendario.appendChild(celda);
            }
        }
        function cambiar_mes(dir) {
            mes_actual += dir;
            if (mes_actual < 0) { mes_actual = 11; anio_actual--; }
            else if (mes_actual > 11) { mes_actual = 0; anio_actual++; }
            generar_calendario(mes_actual, anio_actual);
        }
        function preparar_edicion(id, titulo, fecha, hora, tipo) {
            document.getElementById("editIdInput").value = id;
            document.getElementById("tituloInput").value = titulo;
            document.getElementById("horaInput").value = hora;
            document.getElementById("tipoInput").value = tipo;
            const partes = fecha.split('/');
            document.getElementById("fechaInput").value = `${partes[2]}-${partes[1]}-${partes[0]}`;
            modal.style.display = "flex";
        }
        function cerrar_modal() { modal.style.display = "none"; document.getElementById("formEvento").reset(); document.getElementById("editIdInput").value = ""; }
        function guardar_evento(e) {
            e.preventDefault();
            const id = document.getElementById("editIdInput").value;
            // CAMBIO: Apunta a acciones
            const url = id !== "" ? "acciones/editar_evento.php" : "acciones/guardar_evento.php";
            const data = {
                id: id,
                titulo: document.getElementById("tituloInput").value,
                fecha: document.getElementById("fechaInput").value,
                hora: document.getElementById("horaInput").value,
                tipo: document.getElementById("tipoInput").value
            };
            fetch(url, { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(data) })
            .then(() => window.location.reload());
        }
        function actualizarEstadosAnunciosDashboard() {
            let readNotifs = JSON.parse(localStorage.getItem('read_notifications') || '[]');
            document.querySelectorAll('.evento-item.anuncio').forEach(el => {
                const id = el.getAttribute('data-id');
                const notifId = `ann_${id}`;
                const estadoEl = document.getElementById(`anuncio_estado_${id}`);
                if (estadoEl) {
                    if (readNotifs.includes(notifId)) {
                        estadoEl.innerHTML = '<span class="estado-leido">Leído</span>';
                        el.style.opacity = '0.7';
                    } else {
                        estadoEl.innerHTML = '<span class="estado-nuevo">🔵 Nuevo</span>';
                        el.style.opacity = '1';
                    }
                }
            });
        }

        function verDetalleAnuncioDashboard(element) {
            const id = element.getAttribute('data-id');
            const titulo = element.getAttribute('data-titulo');
            const descripcion = element.getAttribute('data-descripcion');
            const fecha = element.getAttribute('data-fecha');
            
            if (typeof marcarComoLeida === 'function') {
                marcarComoLeida(`ann_${id}`);
            }
            
            actualizarEstadosAnunciosDashboard();
            
            if (typeof mostrarModalNotif === 'function') {
                mostrarModalNotif(titulo, descripcion, fecha, '📢 Anuncio del centro');
            } else {
                alert(`📢 ANUNCIO DEL CENTRO\n\nTítulo: ${titulo}\n\nDetalle: ${descripcion}`);
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            generar_calendario(mes_actual, anio_actual);
            actualizarEstadosAnunciosDashboard();
        });
    </script>
</body>
</html>