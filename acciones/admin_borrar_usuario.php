<?php
session_start();
if (!isset($_SESSION['token']) || $_SESSION['rol'] !== 'admin' || !isset($_GET['id'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'No autorizado']);
    exit();
}

$id = $_GET['id'];

require_once '../componentes/api_client.php';
    $__res = llamar_api("/usuarios/" . $id, "DELETE");
    $respuesta = $__res['respuesta'];
    $http_code = $__res['http_code'];

$resp_data = json_decode($respuesta, true);

if ($http_code == 200) {
    echo json_encode(['exito' => true, 'mensaje' => 'Usuario borrado']);
} else {
    echo json_encode(['exito' => false, 'mensaje' => $resp_data['mensaje'] ?? 'Error desconocido']);
}
