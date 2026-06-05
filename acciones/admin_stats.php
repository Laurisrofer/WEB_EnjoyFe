<?php
session_start();
if (!isset($_SESSION['token']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(['exito' => false, 'mensaje' => 'No autorizado']);
    exit();
}

$url = "http://127.0.0.1:5000/academico/admin-stats";
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

if ($http_code == 200) {
    echo $respuesta;
} else {
    echo json_encode(['exito' => false, 'mensaje' => 'Error al cargar estadísticas']);
}
