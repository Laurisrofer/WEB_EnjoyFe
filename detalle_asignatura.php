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
$estilos_adicionales = '<link rel="stylesheet" href="recursos/detalle_asignatura.css">';

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

                    <div id="Guia" class="tab-content active">
                        <?php echo $detalle['guia_docente']; ?>
                    </div>

                    <div id="Recursos" class="tab-content">
                        <div class="seccion-recursos">
                            <h3>Material adjunto</h3>
                            <?php if (empty($detalle['recursos'])): ?>
                                <p>No hay recursos subidos por el profesor en este momento.</p>
                            <?php else: ?>
                                <?php foreach ($detalle['recursos'] as $recurso): ?>
                                    <a href="<?php echo htmlspecialchars($recurso['url']); ?>" class="recurso-link" target="_blank">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 5px;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg> <?php echo htmlspecialchars($recurso['titulo']); ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="seccion-recursos">
                            <h3>Enlaces de interés</h3>
                            <?php if (empty($detalle['enlaces_interes'])): ?>
                                <p>No hay enlaces adicionales en este momento.</p>
                            <?php else: ?>
                                <?php foreach ($detalle['enlaces_interes'] as $enlace): ?>
                                    <a href="<?php echo htmlspecialchars($enlace['url']); ?>" class="recurso-link" target="_blank">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 5px;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg> <?php echo htmlspecialchars($enlace['titulo']); ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div> <script>
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