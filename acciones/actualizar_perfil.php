<?php
session_start();

// Si el usuario no tiene token, no tiene permiso para actualizar nada
if (!isset($_SESSION['token'])) {
    http_response_code(401);
    echo json_encode(["mensaje" => "No autorizado"]);
    exit();
}

// Recibimos los datos que vienen del formulario (JSON)
$data_json = file_get_contents("php://input");

// Conectamos con el endpoint PUT de nuestro backend en Python
$url = "http://127.0.0.1:5000/academico/perfil";
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $_SESSION['token'],
    'Content-Type: application/json'
]);

$respuesta = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Devolvemos la respuesta exacta de Python al navegador (JavaScript)
http_response_code($http_code);
echo $respuesta;
?>