<?php
session_start();

if (!isset($_SESSION['token'])) {
    http_response_code(401);
    echo json_encode(["mensaje" => "No autorizado"]);
    exit();
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'obtener') {
    // Obtener bandeja de entrada
    $url = "http://127.0.0.1:5000/mensajes";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $_SESSION['token']
    ]);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    http_response_code($code);
    header('Content-Type: application/json');
    echo $res;
    exit();

} elseif ($action === 'contactos') {
    // Obtener lista de usuarios para la agenda/buscador
    $url = "http://127.0.0.1:5000/usuarios/contactos";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $_SESSION['token']
    ]);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    http_response_code($code);
    header('Content-Type: application/json');
    echo $res;
    exit();

} elseif ($action === 'enviar') {
    // Enviar un mensaje
    $destinatario = isset($_POST['destinatario']) ? $_POST['destinatario'] : '';
    $asunto = isset($_POST['asunto']) ? $_POST['asunto'] : 'Sin asunto';
    $cuerpo = isset($_POST['cuerpo']) ? $_POST['cuerpo'] : '';
    
    $adjunto_nombre = null;
    
    // Procesar archivo adjunto si existe y no hay errores
    if (isset($_FILES['adjunto']) && $_FILES['adjunto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Sanitizar el nombre de archivo y añadir timestamp para evitar colisiones
        $file_name = time() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "_", basename($_FILES['adjunto']['name']));
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['adjunto']['tmp_name'], $target_file)) {
            $adjunto_nombre = 'uploads/' . $file_name;
        }
    }
    
    // Enviar datos en formato JSON al backend de Python
    $data = [
        "destinatario_usuario" => $destinatario,
        "asunto" => $asunto,
        "cuerpo" => $cuerpo,
        "adjunto" => $adjunto_nombre
    ];
    
    $url = "http://127.0.0.1:5000/mensajes";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $_SESSION['token'],
        'Content-Type: application/json'
    ]);
    
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    http_response_code($code);
    header('Content-Type: application/json');
    echo $res;
    exit();
} elseif ($action === 'eliminar') {
    // Eliminar un mensaje
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(["mensaje" => "ID no proporcionado"]);
        exit();
    }
    
    $url = "http://127.0.0.1:5000/mensajes/" . $id;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $_SESSION['token']
    ]);
    
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    http_response_code($code);
    header('Content-Type: application/json');
    echo $res;
    exit();
} else {
    http_response_code(400);
    echo json_encode(["mensaje" => "Acción no válida"]);
    exit();
}
?>
