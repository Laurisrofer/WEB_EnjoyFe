<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// 1. Obtener cursos
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

// 2. Obtener profesores para el selector de tutor
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
$pagina_id = 'gestion_cursos';
$titulo_seccion = 'Gestión de cursos';
$estilos_adicionales = '<link rel="stylesheet" href="recursos/dashboard.css?v=1.2">';
include __DIR__ . '/../componentes/header.php';
?>

        <div class="contenedor-datos">
            <div class="tarjeta">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <h2 style="margin:0;">Listado de Cursos</h2>
                    </div>
                    <button class="btn btn-primario btn-sm" onclick="abrirModalCurso()" style="display:flex; align-items:center; gap: 8px; border-radius:20px; padding: 6px 16px; background-color:var(--success-color); border:none; font-weight:bold; cursor:pointer; color:white; font-size:14px;">
                        <span style="display:flex; align-items:center; justify-content:center; width:22px; height:22px; background:rgba(255,255,255,0.3); border-radius:50%; font-size:18px; line-height:1;">+</span> 
                        Añadir nuevo curso
                    </button>
                </div>
                
                <div id="caja_notificacion" class="notificacion"></div>

                <div class="table-responsive">
                    <table class="tabla-academica">
                        <thead>
                            <tr>
                                <th style="text-align: center;">Nombre del Curso</th>
                                <th style="text-align: center;">Descripción</th>
                                <th style="text-align: center;">Tutor asignado</th>
                                <th style="text-align: center;">Asignaturas</th>
                                <th style="text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($cursos)): ?>
                                <tr>
                                    <td colspan="5" class="estado-vacio">No hay cursos registrados.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($cursos as $c): ?>
                                    <?php 
                                        $tutor_nombre = "Sin tutor";
                                        foreach ($profesores as $p) {
                                            if ($p['id'] == $c['id_tutor']) {
                                                $tutor_nombre = $p['nombre_completo'];
                                                break;
                                            }
                                        }
                                        $num_asignaturas = isset($c['asignaturas']) ? count($c['asignaturas']) : 0;
                                    ?>
                                    <tr>
                                        <td style="text-align: center;"><strong><?php echo htmlspecialchars($c['nombre']); ?></strong></td>
                                        <td style="text-align: center;"><?php echo htmlspecialchars($c['descripcion'] ?? '-'); ?></td>
                                        <td style="text-align: center;"><?php echo htmlspecialchars($tutor_nombre); ?></td>
                                        <td style="text-align: center;"><?php echo $num_asignaturas; ?> asignaturas</td>
                                        <td style="text-align: center; white-space: nowrap;">
                                            <button class="btn-icon" title="Editar curso" onclick='prepararEdicion(<?php echo json_encode($c); ?>)' style="margin-right: 5px;">✏️</button>
                                            <button class="btn-icon" title="Eliminar curso" onclick="confirmarEliminar(<?php echo $c['id']; ?>)">❌</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL CURSO -->
    <div id="modalCurso" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Nuevo Curso</h3>
            </div>
            <form id="formCurso" onsubmit="guardarCurso(event)">
                <input type="hidden" id="editIdInput">
                <div class="form-group">
                    <label>Nombre del curso</label>
                    <input type="text" id="nombreInput" required placeholder="Ej: 1º DAM">
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea id="descInput" rows="3" placeholder="Descripción breve del curso..." style="width: 100%; box-sizing: border-box; resize: none; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; font-family: inherit; font-size: 1rem; margin-top: 5px;"></textarea>
                </div>
                <div class="form-group">
                    <label>Tutor asignado</label>
                    <select id="tutorInput">
                        <option value="">-- Sin tutor --</option>
                        <?php foreach ($profesores as $p): ?>
                            <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nombre_completo']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="btn-guardar">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById("modalCurso");
        
        // Mantener las asignaturas originales al editar para no borrarlas accidentalmente
        let asignaturas_actuales = [];
        
        function mostrarMensaje(texto, es_error) {
            const caja = document.getElementById('caja_notificacion');
            caja.style.display = 'block';
            caja.innerText = texto;
            caja.className = es_error ? 'notificacion error' : 'notificacion exito';
            setTimeout(() => { caja.style.display = 'none'; }, 5000);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function abrirModalCurso() {
            document.getElementById("formCurso").reset();
            document.getElementById("editIdInput").value = "";
            document.getElementById("modalTitle").innerText = "Nuevo Curso";
            asignaturas_actuales = [];
            modal.style.display = "flex";
        }

        function prepararEdicion(curso) {
            document.getElementById("formCurso").reset();
            document.getElementById("editIdInput").value = curso.id;
            document.getElementById("modalTitle").innerText = "Editar Curso";
            
            document.getElementById("nombreInput").value = curso.nombre;
            document.getElementById("descInput").value = curso.descripcion || "";
            document.getElementById("tutorInput").value = curso.id_tutor || "";
            
            asignaturas_actuales = curso.asignaturas || [];
            modal.style.display = "flex";
        }

        function cerrarModal() {
            modal.style.display = "none";
        }

        function guardarCurso(e) {
            e.preventDefault();
            const id = document.getElementById("editIdInput").value;
            const isEdit = id !== "";
            const url = isEdit ? `acciones/admin_editar_curso.php?id=${id}` : `acciones/admin_crear_curso.php`;
            
            const data = {
                nombre: document.getElementById("nombreInput").value,
                descripcion: document.getElementById("descInput").value,
                id_tutor: document.getElementById("tutorInput").value || null
            };
            
            // Si es edición, debemos enviar las asignaturas actuales para no sobreescribirlas con vacío
            if (isEdit) {
                data.asignaturas = asignaturas_actuales;
            }

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
                'Eliminar Curso',
                '¿Estás seguro de que deseas eliminar este curso? Se eliminarán también todas sus asignaturas, horarios y datos asociados. Esta acción NO se puede deshacer.',
                function() {
                    fetch(`acciones/admin_borrar_curso.php?id=${id}`, { method: 'GET' })
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
