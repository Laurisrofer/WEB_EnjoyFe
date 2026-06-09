<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// 1. Obtener cursos (con sus asignaturas)
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

// 2. Obtener profesores
$url_usuarios = "http://127.0.0.1:5000/usuarios"; 
$ch2 = curl_init($url_usuarios);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HTTPGET, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $_SESSION['token'],
    'Content-Type: application/json'
]);
$res_usuarios = curl_exec($ch2);
$usuarios = curl_getinfo($ch2, CURLINFO_HTTP_CODE) == 200 ? json_decode($res_usuarios, true) : [];
curl_close($ch2);

$profesores = array_filter($usuarios, function($u) { return strtolower($u['rol']) === 'profesor'; });

// --- CONFIGURACIÓN DEL HEADER ---
$pagina_id = 'gestion_asignaturas';
$titulo_seccion = 'Gestión de asignaturas';
$estilos_adicionales = '<link rel="stylesheet" href="recursos/dashboard.css?v=' . time() . '">';
include __DIR__ . '/../componentes/header.php';
?>

        <div class="contenedor-datos">
            <div class="tarjeta">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <h2 style="margin:0;">Asignaturas por Curso</h2>
                    </div>
                    <button class="btn btn-primario btn-sm" onclick="abrirModalAsignatura()" style="display:flex; align-items:center; gap: 8px; border-radius:20px; padding: 6px 16px; background-color:var(--success-color); border:none; font-weight:bold; cursor:pointer; color:white; font-size:14px;">
                        <span style="display:flex; align-items:center; justify-content:center; width:22px; height:22px; background:rgba(255,255,255,0.3); border-radius:50%; font-size:18px; line-height:1;">+</span> 
                        Añadir nueva asignatura
                    </button>
                </div>
                
                <div id="caja_notificacion" class="notificacion"></div>

                <?php if (empty($cursos)): ?>
                    <div class="estado-vacio">No hay cursos registrados. Crea un curso primero.</div>
                <?php else: ?>
                    <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px; background: var(--input-bg); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color);">
                        <label style="font-weight: bold; margin: 0;">Selecciona el curso para ver sus asignaturas:</label>
                        <select id="selectorCursoFiltro" class="form-control" style="width: auto; padding: 8px 15px; border-radius: 6px; border: 1px solid var(--border-color);" onchange="filtrarVistaCursos(this.value)">
                            <option value="">-- Seleccione un curso --</option>
                            <?php foreach ($cursos as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="mensaje_seleccione_curso" style="text-align: center; padding: 40px; color: var(--text-muted); background: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color);">
                        Seleccione un curso en el desplegable superior para ver y gestionar sus asignaturas.
                    </div>

                    <?php foreach ($cursos as $c): ?>
                        <?php 
                            $tutor_curso = "Sin asignar";
                            foreach ($profesores as $p) {
                                if ($p['id'] == $c['id_tutor']) {
                                    $tutor_curso = $p['nombre_completo'];
                                    break;
                                }
                            }
                        ?>
                        <div id="vista_curso_<?php echo $c['id']; ?>" class="curso-bloque" style="margin-bottom: 30px; display: none;">
                            <div style="border-bottom: 2px solid var(--primary-color); padding-bottom: 10px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: flex-end;">
                                <h3 style="margin: 0; color: var(--primary-color);">
                                    <?php echo htmlspecialchars($c['nombre']); ?>
                                </h3>
                                <span style="font-size: 0.9em; color: var(--text-muted);">Tutor del curso: <strong><?php echo htmlspecialchars($tutor_curso); ?></strong></span>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="tabla-academica">
                                    <thead>
                                        <tr>
                                            <th style="text-align: left;">Nombre de la Asignatura</th>
                                            <th style="text-align: center;">Profesor Titular</th>
                                            <th style="text-align: center;">Horarios</th>
                                            <th style="text-align: center;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($c['asignaturas'])): ?>
                                            <tr>
                                                <td colspan="5" class="estado-vacio">No hay asignaturas en este curso.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($c['asignaturas'] as $a): ?>
                                                <?php 
                                                    $profe_nombre = "Sin asignar";
                                                    foreach ($profesores as $p) {
                                                        if ($p['id'] == $a['id_profesor']) {
                                                            $profe_nombre = $p['nombre_completo'];
                                                            break;
                                                        }
                                                    }
                                                    $num_horarios = isset($a['horarios']) ? count($a['horarios']) : 0;
                                                ?>
                                                <tr>
                                                    <td style="text-align: left;"><strong><?php echo htmlspecialchars($a['nombre']); ?></strong></td>
                                                    <td style="text-align: center;"><?php echo htmlspecialchars($profe_nombre); ?></td>
                                                    <td style="text-align: center;"><?php echo $num_horarios; ?> horas</td>
                                                    <td style="text-align: center; white-space: nowrap;">
                                                        <button class="btn-icon" title="Editar asignatura" onclick='prepararEdicion(<?php echo json_encode($a); ?>, <?php echo $c['id']; ?>)' style="margin-right: 5px;">✏️</button>
                                                        <button class="btn-icon" title="Eliminar asignatura" onclick="confirmarEliminar(<?php echo $a['id']; ?>)">❌</button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- MODAL ASIGNATURA -->
    <div id="modalAsignatura" class="modal">
        <div class="modal-content" style="width: 1200px; max-width: 95%; max-height: 85vh; overflow-y: auto;">
            <div class="modal-header">
                <h3 id="modalTitle">Nueva Asignatura</h3>
            </div>
            <form id="formAsignatura" onsubmit="guardarAsignatura(event)">
                <input type="hidden" id="editIdInput">
                
                <div class="form-group">
                    <label>Curso al que pertenece</label>
                    <select id="cursoInput" required>
                        <option value="">-- Selecciona un curso --</option>
                        <?php foreach ($cursos as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Nombre de la asignatura</label>
                    <input type="text" id="nombreInput" required placeholder="Ej: Bases de Datos">
                </div>
                
                <div class="form-group">
                    <label>Profesor titular</label>
                    <select id="profesorInput">
                        <option value="">-- Sin asignar --</option>
                        <?php foreach ($profesores as $p): ?>
                            <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nombre_completo']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Guía Docente</label>
                    <div id="editor-container" style="height: 150px; background: white; color: #121212; border-bottom-left-radius: 6px; border-bottom-right-radius: 6px; border: 1px solid var(--border-color);"></div>
                    <input type="hidden" id="guiaInput">
                </div>

                <div class="form-group">
                    <label>Recursos Adjuntos</label>
                    <div id="recursosList" style="margin-bottom: 10px; display: flex; flex-direction: column; gap: 5px;"></div>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="nuevoRecursoTitulo" placeholder="Título (Ej: Apuntes tema 1)" style="flex: 1; padding: 5px; border-radius: 4px; border: 1px solid var(--border-color);">
                        <input type="url" id="nuevoRecursoUrl" placeholder="URL (Ej: https://...)" style="flex: 1; padding: 5px; border-radius: 4px; border: 1px solid var(--border-color);">
                        <button type="button" onclick="agregarRecursoAdmin()" style="padding: 6px 12px; background-color: var(--success-color); color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Añadir</button>
                    </div>
                    <input type="hidden" id="recursosInput">
                </div>
                
                <div class="form-group" style="border-top: 1px solid var(--border-color); padding-top: 15px; margin-top: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <label style="margin: 0;">Horarios de la Asignatura</label>
                        <button type="button" style="border-radius:20px; padding: 5px 15px; background-color: var(--success-color); color: white; border: none; font-weight:bold; cursor:pointer;" onclick="agregarFilaHorario()">+ Añadir Tramo</button>
                    </div>
                    <div id="horariosContainer" style="margin-top: 10px; display: flex; flex-direction: column; gap: 10px;">
                        <!-- Filas de horario generadas por JS -->
                    </div>
                </div>
                
                <div class="form-actions" style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                    <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="btn-guardar">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script>
        function filtrarVistaCursos(idCurso) {
            document.querySelectorAll('.curso-bloque').forEach(el => {
                el.style.display = 'none';
            });
            const mensaje = document.getElementById('mensaje_seleccione_curso');
            
            if (!idCurso) {
                if (mensaje) mensaje.style.display = 'block';
                return;
            }
            
            if (mensaje) mensaje.style.display = 'none';
            const bloque = document.getElementById('vista_curso_' + idCurso);
            if (bloque) bloque.style.display = 'block';
        }

        const modal = document.getElementById("modalAsignatura");
        let horariosActivos = [];
        let recursosAdmin = [];
        let idContador = 0;
        let quillAdmin;

        document.addEventListener("DOMContentLoaded", function() {
            const BlockEmbed = Quill.import('blots/block/embed');
            class DividerBlot extends BlockEmbed { }
            DividerBlot.blotName = 'divider';
            DividerBlot.tagName = 'hr';
            Quill.register(DividerBlot);

            quillAdmin = new Quill('#editor-container', {
                theme: 'snow',
                placeholder: 'Escribe la guía docente aquí...'
            });
        });

        function renderRecursosAdmin() {
            const container = document.getElementById('recursosList');
            container.innerHTML = '';
            if (recursosAdmin.length === 0) {
                container.innerHTML = '<span style="color:var(--text-muted); font-size:0.9em; font-style:italic;">Sin recursos adjuntos.</span>';
            } else {
                recursosAdmin.forEach((r, idx) => {
                    const div = document.createElement('div');
                    div.style = "display:flex; justify-content:space-between; align-items:center; background:var(--input-bg); border:1px solid var(--border-color); padding:5px 10px; border-radius:4px; font-size:0.9em;";
                    div.innerHTML = `<span><a href="${escapeHtml(r.url)}" target="_blank" style="color:var(--primary-color); text-decoration:none;">${escapeHtml(r.titulo)}</a></span> <button type="button" class="btn-icon" style="color:var(--danger-color); font-size:12px;" onclick="eliminarRecursoAdmin(${idx})">❌</button>`;
                    container.appendChild(div);
                });
            }
            document.getElementById('recursosInput').value = JSON.stringify(recursosAdmin);
        }

        function agregarRecursoAdmin() {
            const tit = document.getElementById('nuevoRecursoTitulo').value.trim();
            const url = document.getElementById('nuevoRecursoUrl').value.trim();
            if(tit && url) {
                recursosAdmin.push({titulo: tit, url: url, tipo: 'link'});
                renderRecursosAdmin();
                document.getElementById('nuevoRecursoTitulo').value = '';
                document.getElementById('nuevoRecursoUrl').value = '';
            }
        }

        function eliminarRecursoAdmin(idx) {
            recursosAdmin.splice(idx, 1);
            renderRecursosAdmin();
        }

        function renderHorarios() {
            const container = document.getElementById('horariosContainer');
            container.innerHTML = '';
            if (horariosActivos.length === 0) {
                container.innerHTML = '<div style="color: var(--text-muted); font-size: 0.9em; font-style: italic;">Sin horarios asignados.</div>';
                return;
            }
            
            horariosActivos.forEach(h => {
                const div = document.createElement('div');
                div.style.display = 'flex';
                div.style.gap = '10px';
                div.style.alignItems = 'center';
                
                div.innerHTML = `
                    <select id="dia_${h._id}" style="width: 120px; flex-shrink: 0; padding: 5px; border-radius: 4px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-color);" onchange="actualizarHorario(${h._id}, 'dia_semana', this.value)">
                        <option value="1" ${h.dia_semana == 1 ? 'selected' : ''}>Lunes</option>
                        <option value="2" ${h.dia_semana == 2 ? 'selected' : ''}>Martes</option>
                        <option value="3" ${h.dia_semana == 3 ? 'selected' : ''}>Miércoles</option>
                        <option value="4" ${h.dia_semana == 4 ? 'selected' : ''}>Jueves</option>
                        <option value="5" ${h.dia_semana == 5 ? 'selected' : ''}>Viernes</option>
                    </select>
                    <input type="time" id="inicio_${h._id}" value="${h.hora_inicio.substring(0,5)}" style="width: 90px; flex-shrink: 0; padding: 5px; border-radius: 4px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-color);" onchange="actualizarHorario(${h._id}, 'hora_inicio', this.value)">
                    <span>-</span>
                    <input type="time" id="fin_${h._id}" value="${h.hora_fin.substring(0,5)}" style="width: 90px; flex-shrink: 0; padding: 5px; border-radius: 4px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-color);" onchange="actualizarHorario(${h._id}, 'hora_fin', this.value)">
                    <button type="button" class="btn-icon" style="color: var(--danger-color); margin-left: auto;" onclick="eliminarHorario(${h._id})">❌</button>
                `;
                container.appendChild(div);
            });
        }
        
        function agregarFilaHorario() {
            horariosActivos.push({
                _id: idContador++,
                dia_semana: 1,
                hora_inicio: "08:00",
                hora_fin: "09:00"
            });
            renderHorarios();
        }
        
        function eliminarHorario(id) {
            horariosActivos = horariosActivos.filter(h => h._id !== id);
            renderHorarios();
        }
        
        function actualizarHorario(id, campo, valor) {
            const index = horariosActivos.findIndex(h => h._id === id);
            if (index > -1) {
                horariosActivos[index][campo] = valor;
            }
        }
        
        function mostrarMensaje(texto, es_error) {
            const caja = document.getElementById('caja_notificacion');
            caja.style.display = 'block';
            caja.innerText = texto;
            caja.className = es_error ? 'notificacion error' : 'notificacion exito';
            setTimeout(() => { caja.style.display = 'none'; }, 5000);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function abrirModalAsignatura() {
            document.getElementById("formAsignatura").reset();
            document.getElementById("editIdInput").value = "";
            document.getElementById("modalTitle").innerText = "Nueva Asignatura";
            quillAdmin.root.innerHTML = `<p><strong>Descripción de la asignatura</strong></p><p>Esta asignatura introduce al estudiante en el funcionamiento del hardware, la instalación y administración de sistemas operativos y la gestión de recursos de red locales.</p><p><strong>Bloques temáticos</strong></p><hr><ul><li>Introducción a los sistemas informáticos y arquitectura física.</li><li>Instalación de sistemas operativos libres y propietarios.</li><li>Gestión del almacenamiento, particionamiento y sistemas de archivos.</li><li>Administración avanzada de usuarios, permisos y automatización (scripts).</li><li>Configuración básica de red local y servicios básicos.</li></ul><p><strong>Criterios de evaluación</strong></p><hr><ul><li><strong>60%</strong> Exámenes teóricos y prácticos escritos.</li><li><strong>30%</strong> Proyectos prácticos de laboratorio/programación.</li><li><strong>10%</strong> Asistencia activa y entrega de tareas.</li></ul>`;
            recursosAdmin = [];
            renderRecursosAdmin();
            horariosActivos = [];
            renderHorarios();
            modal.style.display = "flex";
        }

        function prepararEdicion(asignatura, id_curso) {
            document.getElementById("formAsignatura").reset();
            document.getElementById("editIdInput").value = asignatura.id;
            document.getElementById("modalTitle").innerText = "Editar Asignatura";
            
            document.getElementById("cursoInput").value = id_curso;
            document.getElementById("nombreInput").value = asignatura.nombre;
            document.getElementById("profesorInput").value = asignatura.id_profesor || "";
            
            // Procesar horarios existentes
            horariosActivos = [];
            if (asignatura.horarios && asignatura.horarios.length > 0) {
                asignatura.horarios.forEach(h => {
                    horariosActivos.push({
                        _id: idContador++,
                        dia_semana: h.dia_semana,
                        hora_inicio: h.hora_inicio || "00:00",
                        hora_fin: h.hora_fin || "00:00"
                    });
                });
            }
            renderHorarios();
            
            const defaultTemplate = `<p><strong>Descripción de la asignatura</strong></p><p>Esta asignatura introduce al estudiante en el funcionamiento del hardware, la instalación y administración de sistemas operativos y la gestión de recursos de red locales.</p><p><strong>Bloques temáticos</strong></p><hr><ul><li>Introducción a los sistemas informáticos y arquitectura física.</li><li>Instalación de sistemas operativos libres y propietarios.</li><li>Gestión del almacenamiento, particionamiento y sistemas de archivos.</li><li>Administración avanzada de usuarios, permisos y automatización (scripts).</li><li>Configuración básica de red local y servicios básicos.</li></ul><p><strong>Criterios de evaluación</strong></p><hr><ul><li><strong>60%</strong> Exámenes teóricos y prácticos escritos.</li><li><strong>30%</strong> Proyectos prácticos de laboratorio/programación.</li><li><strong>10%</strong> Asistencia activa y entrega de tareas.</li></ul>`;
            quillAdmin.root.innerHTML = asignatura.guia_docente || defaultTemplate;
            try {
                recursosAdmin = asignatura.recursos_json ? JSON.parse(asignatura.recursos_json) : [];
            } catch(e) {
                recursosAdmin = [];
            }
            renderRecursosAdmin();

            modal.style.display = "flex";
        }

        function cerrarModal() {
            modal.style.display = "none";
        }

        function guardarAsignatura(e) {
            e.preventDefault();
            const id = document.getElementById("editIdInput").value;
            const isEdit = id !== "";
            const url = isEdit ? `acciones/admin_editar_asignatura.php?id=${id}` : `acciones/admin_crear_asignatura.php`;
            
            const data = {
                id_curso: document.getElementById("cursoInput").value,
                nombre: document.getElementById("nombreInput").value,
                id_profesor: document.getElementById("profesorInput").value || null,
                guia_docente: quillAdmin.root.innerHTML,
                recursos_json: document.getElementById("recursosInput").value || null,
                horarios: horariosActivos.map(h => ({
                    dia_semana: parseInt(h.dia_semana),
                    hora_inicio: h.hora_inicio,
                    hora_fin: h.hora_fin
                }))
            };

            fetch(url, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(data => {
                if (data.exito) {
                    window.location.reload();
                } else {
                    cerrarModal();
                    mostrarMensaje(data.mensaje || "Ocurrió un error", true);
                }
            })
            .catch(err => {
                cerrarModal();
                mostrarMensaje("Error de conexión", true);
            });
        }

        function confirmarEliminar(id) {
            mostrarConfirmacionGlobal(
                'Eliminar Asignatura',
                '¿Estás seguro de que deseas eliminar esta asignatura? Se perderán las matriculaciones, horarios y notas. NO se puede deshacer.',
                function() {
                    fetch(`acciones/admin_borrar_asignatura.php?id=${id}`, { method: 'GET' })
                    .then(res => res.json())
                    .then(data => {
                        if (data.exito) {
                            window.location.reload();
                        } else {
                            mostrarMensaje(data.mensaje || "Error al eliminar", true);
                        }
                    })
                    .catch(() => mostrarMensaje("Error de conexión", true));
                }
            );
        }
    </script>

<?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>
