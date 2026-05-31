<?php
// 1. Recogemos los datos del formulario
$usuario = $_POST['usuario'];
$password = $_POST['password'];

$datos_login = [
    'nombre_usuario' => $usuario,
    'contrasena' => $password
];

$json_data = json_encode($datos_login);
$url = "http://127.0.0.1:5000/auth/login";

// ... (mantenemos la parte del $json_data = json_encode($datos_login);)

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
    // Hemos eliminado 'Content-Length' para que cURL lo calcule automáticamente
]);
// ... (resto del código igual)

$respuesta = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 2. Lógica de sesión
if ($http_code == 200) {
    $respuesta_json = json_decode($respuesta, true);
    
    // Iniciamos la sesión
    session_start();
    $_SESSION['token'] = $respuesta_json['token'];
    $_SESSION['rol'] = $respuesta_json['rol'];
    $_SESSION['nombre_usuario'] = $respuesta_json['usuario'];
    
    // CAMBIO: Salimos de 'acciones/' para llegar a 'dashboard.php'
    header("Location: ../dashboard.php");
    exit();
} else {
    // Si falla, mostramos el error
    echo "<h1>Error de acceso (Código: $http_code)</h1>";
    echo "<p>Respuesta de la API: $respuesta</p>";
    // CAMBIO: Salimos de 'acciones/' para llegar a 'index.php'
    echo "<br><a href='../index.php'>Volver al login</a>";
}
?>