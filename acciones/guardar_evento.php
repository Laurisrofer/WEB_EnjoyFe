<?php
session_start();

// Si no hay token de seguridad, bloqueamos
if (!isset($_SESSION['token'])) {
    http_response_code(401);
    echo json_encode(["mensaje" => "No autorizado"]);
    exit();
}

// 1. Recogemos los datos que nos envía el JavaScript
$json_recibido = file_get_contents('php://input');

// 2. Se los enviamos a Python (igual que hacíamos para cargar datos)
$url = "http://127.0.0.1:5000/academico/crear-evento";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_recibido);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $_SESSION['token'],
    'Content-Type: application/json'
]);

// 3. Ejecutamos y le devolvemos la respuesta de Python al JavaScript
$respuesta = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($http_code);
echo $respuesta;
?>