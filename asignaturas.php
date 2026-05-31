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

// --- CONFIGURACIÓN DEL HEADER ---
$pagina_id = 'asignaturas';
$titulo_seccion = 'Mis asignaturas';
$estilos_adicionales = '<link rel="stylesheet" href="recursos/asignaturas.css">';

include 'componentes/header.php';
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
                    <p>Actualmente no estás matriculado/a en ninguna asignatura.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive"><table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre de la asignatura</th>
                            <th>Profesor</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($asignaturas as $asig): ?>
                            <tr>
                                <td><?php echo $asig['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($asig['nombre']); ?></strong></td>
                                <td>Profesor: <?php echo htmlspecialchars($asig['profesor']); ?></td>
                                <td>
                                    <a href="detalle_asignatura.php?id=<?php echo $asig['id']; ?>" class="btn-detalles">Ver detalles</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table></div>
            <?php endif; ?>
        </div>
    </div> </body>
</html>
