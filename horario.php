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

<script src="recursos/js/horario.js"></script>

<?php include 'componentes/footer.php'; ?>
</body>
</html>
