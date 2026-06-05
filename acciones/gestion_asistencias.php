<?php
session_start();

if (!isset($_SESSION['token'])) {
    http_response_code(401);
    echo json_encode(["mensaje" => "No autorizado"]);
    exit();
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

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

// 1. Obtener asistencias del alumno actual
if ($action === 'get_mis_asistencias') {
    realizarPeticionCurl("http://127.0.0.1:5000/academico/mis-asistencias", 'GET');
}

// 2. Enviar justificante de falta
if ($action === 'save_justificacion') {
    // Comprobar si es JSON (como antes) o FormData (con archivo)
    $content_type = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    
    if (strpos($content_type, 'application/json') !== false) {
        $json_recibido = file_get_contents('php://input');
    } else {
        $id = isset($_POST['id_asistencia']) ? intval($_POST['id_asistencia']) : 0;
        $texto = isset($_POST['justificante_texto']) ? $_POST['justificante_texto'] : '';
        $json_recibido = json_encode(["id_asistencia" => $id, "justificante_texto" => $texto]);
    }
    
    realizarPeticionCurl("http://127.0.0.1:5000/academico/solicitar-justificacion", 'POST', $json_recibido);
}

// 3. Obtener listado de alumnos y sesiones programadas (para profesores)
if ($action === 'get_asistencia_curso') {
    $id_curso = isset($_GET['id_curso']) ? intval($_GET['id_curso']) : 0;
    $id_asignatura = isset($_GET['id_asignatura']) ? intval($_GET['id_asignatura']) : 0;
    $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
    $hora = isset($_GET['hora']) ? $_GET['hora'] : '';
    
    $query = http_build_query([
        "id_curso" => $id_curso,
        "id_asignatura" => $id_asignatura,
        "fecha" => $fecha,
        "hora" => $hora
    ]);
    
    realizarPeticionCurl("http://127.0.0.1:5000/academico/asistencia-curso?" . $query, 'GET');
}

// 4. Guardar la lista de asistencia del grupo
if ($action === 'save_asistencias') {
    $json_recibido = file_get_contents('php://input');
    realizarPeticionCurl("http://127.0.0.1:5000/academico/guardar-asistencias", 'POST', $json_recibido);
}

// 5. Obtener justificaciones pendientes
if ($action === 'get_justificaciones') {
    realizarPeticionCurl("http://127.0.0.1:5000/academico/justificaciones-pendientes", 'GET');
}

// 6. Resolver justificación (aprobar o rechazar)
if ($action === 'resolver_justificacion') {
    $json_recibido = file_get_contents('php://input');
    realizarPeticionCurl("http://127.0.0.1:5000/academico/resolver-justificacion", 'POST', $json_recibido);
}

// Acción no válida
http_response_code(400);
echo json_encode(["mensaje" => "Acción no válida"]);
?>
