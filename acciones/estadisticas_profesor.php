<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['token'])) {
    http_response_code(401);
    echo json_encode(["mensaje" => "No autorizado"]);
    exit;
}

$id_asignatura = isset($_GET['id_asignatura']) ? $_GET['id_asignatura'] : '';
$url = "http://127.0.0.1:5000/academico/estadisticas-profesor";
if ($id_asignatura) {
    $url .= "?id_asignatura=" . urlencode($id_asignatura);
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $_SESSION['token'],
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($httpCode);
echo $response;
?>
