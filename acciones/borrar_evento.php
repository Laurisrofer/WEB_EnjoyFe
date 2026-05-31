<?php
session_start();

if (!isset($_GET['id'])) {
    die("Error: No se recibió el ID para borrar.");
}

$id = $_GET['id'];
$url = "http://127.0.0.1:5000/academico/borrar-evento/" . $id;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $_SESSION['token'],
    'Content-Type: application/json'
]);

$respuesta = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Si el código NO es 200 (OK), mostramos el error
if ($http_code != 200) {
    echo "<h1>Error al borrar</h1>";
    echo "<p>Código HTTP: $http_code</p>";
    echo "<p>Respuesta del servidor: " . htmlspecialchars($respuesta) . "</p>";
    echo "<p>Error de cURL: " . $error . "</p>";
    // CAMBIO: Añadimos ../ para salir de la carpeta 'acciones'
    echo "<a href='../dashboard.php'>Volver al Dashboard</a>";
} else {
    // CAMBIO: Añadimos ../ para salir de la carpeta 'acciones'
    header("Location: ../dashboard.php");
}
?>