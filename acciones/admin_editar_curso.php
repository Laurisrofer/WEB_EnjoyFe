<?php
session_start();
if (!isset($_SESSION['token']) || $_SESSION['rol'] !== 'admin' || !isset($_GET['id'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'No autorizado']);
    exit();
}

$id = $_GET['id'];
$input = json_decode(file_get_contents('php://input'), true);

$url = "http://127.0.0.1:5000/cursos/" . $id;
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($input));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $_SESSION['token'],
    'Content-Type: application/json'
]);

$respuesta = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$resp_data = json_decode($respuesta, true);

if ($http_code == 200) {
    echo json_encode(['exito' => true, 'mensaje' => 'Curso actualizado']);
} else {
    echo json_encode(['exito' => false, 'mensaje' => $resp_data['mensaje'] ?? 'Error desconocido']);
}
