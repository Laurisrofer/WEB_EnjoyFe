<?php
session_start();

if (!isset($_SESSION['token'])) {
    http_response_code(401);
    echo json_encode(["mensaje" => "No autorizado"]);
    exit();
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

// Función auxiliar para realizar peticiones cURL de forma robusta
function realizarPeticionCurl($url, $method = 'GET', $fields = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    } else {
        curl_setopt($ch, CURLOPT_HTTPGET, true);
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $_SESSION['token'],
        'Content-Type: application/json'
    ]);
    
    $respuesta = curl_exec($ch);
    
    if ($respuesta === false) {
        $error = curl_error($ch);
        curl_close($ch);
        http_response_code(500);
        echo json_encode([
            "mensaje" => "Error de conexión interna cURL con la API de Python",
            "error" => $error
        ]);
        exit();
    }
    
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    http_response_code($http_code);
    header('Content-Type: application/json');
    echo $respuesta;
    exit();
}

// 0. Obtener listado de notas del alumno actual
if ($action === 'get_mis_notas') {
    realizarPeticionCurl("http://127.0.0.1:5000/academico/mis-notas", 'GET');
}

// 1. Obtener listado de cursos, asignaturas y alumnos (para profesores/admin)
if ($action === 'get_notas') {
    realizarPeticionCurl("http://127.0.0.1:5000/academico/notas-profesores", 'GET');
}

// 2. Guardar o actualizar una calificación individual
if ($action === 'save_calif') {
    $json_recibido = file_get_contents('php://input');
    realizarPeticionCurl("http://127.0.0.1:5000/academico/guardar-calificacion", 'POST', $json_recibido);
}

// 3. Guardar la nota final y observaciones globales de una matrícula
if ($action === 'save_final') {
    $json_recibido = file_get_contents('php://input');
    realizarPeticionCurl("http://127.0.0.1:5000/academico/guardar-nota-final", 'POST', $json_recibido);
}

// Acción desconocida
http_response_code(400);
echo json_encode(["mensaje" => "Acción no válida"]);
?>
