<?php
session_start();

// Si no hay token de seguridad, bloqueamos
if (!isset($_SESSION['token'])) {
    http_response_code(401);
    echo json_encode(["mensaje" => "No autorizado"]);
    exit();
}

$id_curso = isset($_GET['id_curso']) ? $_GET['id_curso'] : '';
$url = "http://127.0.0.1:5000/academico/mi-horario" . ($id_curso ? "?id_curso=" . $id_curso : "");

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

// Devolvemos la respuesta exacta al JavaScript del navegador
http_response_code($http_code);
header('Content-Type: application/json');
echo $respuesta;
?>
