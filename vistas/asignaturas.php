<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token'])) {
    header("Location: index.php");
    exit();
}

$url = "http://127.0.0.1:5000/academico/mis-asignaturas"; 
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

$asignaturas = [];
if ($http_code == 200) {
    $asignaturas = json_decode($respuesta, true);
}

$es_profesor = isset($_SESSION['rol']) && $_SESSION['rol'] === 'profesor';
$cursos_profesor = [];

if ($es_profesor) {
    $url_cursos = "http://127.0.0.1:5000/academico/mis-cursos"; 
    $ch2 = curl_init($url_cursos);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_HTTPGET, true);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $_SESSION['token'],
        'Content-Type: application/json'
    ]);
    $resp_cursos = curl_exec($ch2);
    if (curl_getinfo($ch2, CURLINFO_HTTP_CODE) == 200) {
        $cursos_profesor = json_decode($resp_cursos, true);
    }
    curl_close($ch2);
}

// --- CONFIGURACIÓN DEL HEADER ---
$pagina_id = 'asignaturas';
$titulo_seccion = 'Mis asignaturas';
$estilos_adicionales = '<link rel="stylesheet" href="recursos/asignaturas.css?v=1.1">';

include __DIR__ . '/../componentes/header.php';
?>

        <div class="contenedor-datos">
            <h2>Mis asignaturas matriculadas</h2>
            
            <?php if ($http_code != 200): ?>
                <div class="estado-vacio">
                    <h3>Error de conexión</h3>
                    <p>No se han podido cargar las asignaturas (Código de error: <?php echo $http_code; ?>).</p>
                    <p>Respuesta: <?php echo htmlspecialchars($respuesta); ?></p>
                </div>
            <?php elseif (empty($asignaturas)): ?>
                <div class="estado-vacio">
                    <h3>Sin asignaturas</h3>
                    <p>Actualmente no tienes asignaturas asignadas.</p>
                </div>
            <?php else: ?>
                
                <?php if ($es_profesor && !empty($cursos_profesor)): ?>
                    <div style="margin-bottom: 20px;">
                        <label for="cursoFilter" style="font-weight:bold; margin-right: 10px;">Filtrar por curso:</label>
                        <select id="cursoFilter" onchange="filtrarAsignaturas()" style="padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-color);">
                            <option value="">Todos los cursos</option>
                            <?php foreach ($cursos_profesor as $c): ?>
                                <option value="<?php echo htmlspecialchars($c['id']); ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="table-responsive"><table>
                    <thead>
                        <tr>
                            <th>Nombre de la asignatura</th>
                            <?php if (!$es_profesor): ?><th>Profesor</th><?php endif; ?>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaAsignaturas">
                        <?php foreach ($asignaturas as $asig): ?>
                            <tr class="fila-asignatura" data-curso-id="<?php echo htmlspecialchars($asig['id_curso'] ?? ''); ?>">
                                <td><strong><?php echo htmlspecialchars($asig['nombre']); ?></strong></td>
                                <?php if (!$es_profesor): ?>
                                <td>Profesor: <?php echo htmlspecialchars($asig['profesor']); ?></td>
                                <?php endif; ?>
                                <td>
                                    <a href="detalle_asignatura.php?id=<?php echo $asig['id']; ?>" class="btn-detalles">Ver detalles</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table></div>
            <?php endif; ?>
        </div>
    </div> 

    <script>
    function filtrarAsignaturas() {
        const cursoId = document.getElementById('cursoFilter').value;
        const filas = document.querySelectorAll('.fila-asignatura');
        filas.forEach(fila => {
            const rowCurso = fila.getAttribute('data-curso-id');
            if (cursoId === '' || rowCurso === cursoId) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    }
    </script>
<?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>
