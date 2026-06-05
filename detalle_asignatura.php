<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: asignaturas.php");
    exit();
}

$id_asignatura = $_GET['id'];

$url = "http://127.0.0.1:5000/academico/asignatura/" . $id_asignatura; 
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

$detalle = [];
if ($http_code == 200) {
    $detalle = json_decode($respuesta, true);
}

// --- CONFIGURACIÓN DEL HEADER ---
$pagina_id = 'asignaturas';
$titulo_seccion = 'Detalle de asignatura';
$estilos_adicionales = '<link rel="stylesheet" href="recursos/detalle_asignatura.css?v=' . time() . '">';
$puede_editar = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'profesor');

include 'componentes/header.php';
?>

        <div class="contenedor-datos">
            <a href="asignaturas.php" class="btn-volver">⬅ Volver a mis asignaturas</a>
            
            <?php if ($http_code != 200 || empty($detalle)): ?>
                <div class="estado-error">
                    <h3>Asignatura no encontrada</h3>
                    <p>No se han podido cargar los detalles. Es posible que la asignatura no exista o no tengas permisos.</p>
                </div>
            <?php else: ?>
                
                <div class="cabecera-asignatura">
                    <h2><?php echo htmlspecialchars($detalle['nombre']); ?></h2>
                    <p>Profesor: <?php echo htmlspecialchars($detalle['profesor']); ?></p>
                </div>

                <div class="tab-container">
                    <div class="tabs">
                        <button class="tab-button active" onclick="abrir_pestana(event, 'Guia')">📖 Guía docente</button>
                        <button class="tab-button" onclick="abrir_pestana(event, 'Recursos')">📁 Recursos</button>
                    </div>

                    <?php 
                        $plantilla_guia = '<p><strong>Descripción de la asignatura</strong></p><p>Esta asignatura introduce al estudiante en el funcionamiento del hardware, la instalación y administración de sistemas operativos y la gestión de recursos de red locales.</p><p><strong>Bloques temáticos</strong></p><hr><ul><li>Introducción a los sistemas informáticos y arquitectura física.</li><li>Instalación de sistemas operativos libres y propietarios.</li><li>Gestión del almacenamiento, particionamiento y sistemas de archivos.</li><li>Administración avanzada de usuarios, permisos y automatización (scripts).</li><li>Configuración básica de red local y servicios básicos.</li></ul><p><strong>Criterios de evaluación</strong></p><hr><ul><li><strong>60%</strong> Exámenes teóricos y prácticos escritos.</li><li><strong>30%</strong> Proyectos prácticos de laboratorio/programación.</li><li><strong>10%</strong> Asistencia activa y entrega de tareas.</li></ul>';
                        $guia_html = !empty(trim($detalle['guia_docente'])) ? $detalle['guia_docente'] : $plantilla_guia; 
                    ?>
                    <div id="Guia" class="tab-content active">
                        <?php if ($puede_editar): ?>
                            <div style="margin-bottom: 15px; text-align: right;">
                                <button class="btn-icon" style="font-size: 20px; background: transparent; border: none; cursor: pointer;" onclick="toggleEditGuia()" title="Editar Guía Docente">✏️</button>
                            </div>
                            
                            <!-- Modo Vista -->
                            <div id="guiaVista">
                                <?php echo $guia_html; ?>
                            </div>
                            
                            <!-- Modo Edición -->
                            <div id="guiaEdicion" style="display: none;">
                                <div style="background: white; color: black; border-radius: 6px;">
                                    <div id="editor-container" style="height: 300px;"><?php echo $guia_html; ?></div>
                                </div>
                                <div class="form-actions" style="margin-top: 15px; display:flex; justify-content:flex-end; gap:10px;">
                                    <button class="btn-cancelar" onclick="toggleEditGuia()">Cancelar</button>
                                    <button class="btn-guardar" onclick="guardarGuia(<?php echo $id_asignatura; ?>)">Guardar</button>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php echo $guia_html; ?>
                        <?php endif; ?>
                    </div>

                    <div id="Recursos" class="tab-content">
                        <div class="seccion-recursos">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <h3>Material adjunto y Enlaces</h3>
                                <?php if ($puede_editar): ?>
                                    <button class="btn-icon" onclick="abrirModalRecurso()" style="font-size: 20px; background: transparent; border: none; cursor: pointer;" title="Añadir Recurso">➕</button>
                                <?php endif; ?>
                            </div>
                            <div id="listaRecursos">
                                <?php if (empty($detalle['recursos'])): ?>
                                    <p>No hay recursos subidos en este momento.</p>
                                <?php else: ?>
                                    <?php foreach ($detalle['recursos'] as $idx => $recurso): ?>
                                        <div style="display:flex; justify-content:space-between; align-items:center; background:var(--input-bg); border:1px solid var(--border-color); padding:10px; margin-bottom:5px; border-radius:6px;">
                                            <a href="<?php echo htmlspecialchars($recurso['url']); ?>" class="recurso-link" target="_blank" style="flex-grow:1;">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 5px;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg> 
                                                <?php echo htmlspecialchars($recurso['titulo']); ?>
                                            </a>
                                            <?php if ($puede_editar): ?>
                                                <button class="btn-icon" onclick="eliminarRecurso(<?php echo $idx; ?>)" style="margin-left:10px; background:none; border:none; cursor:pointer;" title="Eliminar">❌</button>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div> 
    
    <?php if ($puede_editar): ?>
    <!-- Modal Recurso -->
    <div id="modalRecurso" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:var(--card-bg); padding:20px; border-radius:10px; width:400px; max-width:90%;">
            <h3>Añadir Recurso</h3>
            <div style="margin-bottom:15px;">
                <label>Título del recurso:</label>
                <input type="text" id="recursoTitulo" style="width:100%; padding:8px; border:1px solid var(--border-color); border-radius:6px; background:var(--input-bg); color:var(--text-color);">
            </div>
            <div style="margin-bottom:15px;">
                <label>URL / Enlace:</label>
                <input type="url" id="recursoUrl" style="width:100%; padding:8px; border:1px solid var(--border-color); border-radius:6px; background:var(--input-bg); color:var(--text-color);" placeholder="https://...">
            </div>
            <div style="text-align:right; margin-top:20px; display:flex; justify-content:flex-end; gap:10px;">
                <button class="btn-cancelar" onclick="cerrarModalRecurso()">Cancelar</button>
                <button class="btn-guardar" onclick="guardarNuevoRecurso()">Guardar</button>
            </div>
        </div>
    </div>

    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
    let quill;
    document.addEventListener("DOMContentLoaded", function() {
        const BlockEmbed = Quill.import('blots/block/embed');
        class DividerBlot extends BlockEmbed { }
        DividerBlot.blotName = 'divider';
        DividerBlot.tagName = 'hr';
        Quill.register(DividerBlot);

        if (document.getElementById('editor-container')) {
            quill = new Quill('#editor-container', {
                theme: 'snow'
            });
        }
    });

    const recursosActuales = <?php echo json_encode($detalle['recursos'] ?? []); ?>;
    const idAsignatura = <?php echo $id_asignatura; ?>;

    function toggleEditGuia() {
        const vista = document.getElementById("guiaVista");
        const edicion = document.getElementById("guiaEdicion");
        if (vista.style.display === "none") {
            vista.style.display = "block";
            edicion.style.display = "none";
        } else {
            vista.style.display = "none";
            edicion.style.display = "block";
        }
    }

    function guardarGuia(id) {
        const contenido = quill.root.innerHTML;
        fetch('acciones/editar_guia.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + id + '&guia_docente=' + encodeURIComponent(contenido)
        })
        .then(response => response.json())
        .then(data => {
            if(data.exito) {
                location.reload();
            } else {
                alert("Error al guardar la guía.");
            }
        });
    }

    function abrirModalRecurso() {
        document.getElementById("recursoTitulo").value = '';
        document.getElementById("recursoUrl").value = '';
        document.getElementById("modalRecurso").style.display = "flex";
    }

    function cerrarModalRecurso() {
        document.getElementById("modalRecurso").style.display = "none";
    }

    function guardarNuevoRecurso() {
        const titulo = document.getElementById("recursoTitulo").value;
        const url = document.getElementById("recursoUrl").value;
        if(titulo && url) {
            recursosActuales.push({titulo: titulo, url: url, tipo: 'link'});
            actualizarRecursos();
        }
    }

    function eliminarRecurso(idx) {
        if(confirm("¿Seguro que deseas eliminar este recurso?")) {
            recursosActuales.splice(idx, 1);
            actualizarRecursos();
        }
    }

    function actualizarRecursos() {
        fetch('acciones/editar_recursos.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + idAsignatura + '&recursos=' + encodeURIComponent(JSON.stringify(recursosActuales))
        })
        .then(response => response.json())
        .then(data => {
            if(data.exito) {
                location.reload();
            } else {
                alert("Error al actualizar recursos.");
            }
        });
    }
    </script>
    <?php endif; ?>
    
    <script>
    function abrir_pestana(evento, nombre_pestana) {
        var i, tab_content, tab_links;
        
        tab_content = document.getElementsByClassName("tab-content");
        for (i = 0; i < tab_content.length; i++) {
            tab_content[i].style.display = "none";
            tab_content[i].classList.remove("active");
        }
        
        tab_links = document.getElementsByClassName("tab-button");
        for (i = 0; i < tab_links.length; i++) {
            tab_links[i].classList.remove("active");
        }
        
        document.getElementById(nombre_pestana).style.display = "block";
        document.getElementById(nombre_pestana).classList.add("active");
        evento.currentTarget.classList.add("active");
    }
    </script>
</body>
</html>