<?php
session_start();
if (!isset($_SESSION['token']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(['exito' => false, 'mensaje' => 'No autorizado']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

$url = "http://127.0.0.1:5000/academico/asignatura";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($input));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $_SESSION['token'],
    'Content-Type: application/json'
]);

$respuesta = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$resp_data = json_decode($respuesta, true);

if ($http_code == 201) {
    echo json_encode(['exito' => true, 'mensaje' => 'Asignatura creada']);
} else {
    echo json_encode(['exito' => false, 'mensaje' => $resp_data['mensaje'] ?? 'Error desconocido']);
}
