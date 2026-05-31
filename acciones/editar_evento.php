<?php
session_start();

// Recibimos los datos que vienen del JavaScript
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];

// Configuramos la llamada al API de Python
$url = "http://127.0.0.1:5000/academico/editar-evento/" . $id;
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // Método PUT para editar
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $_SESSION['token'],
    'Content-Type: application/json'
]);

// Ejecutamos la petición
curl_exec($ch);
curl_close($ch);
?>