<?php
/**
 * CONTROLADOR: Procesar Login
 * Este script actúa como intermediario (Controlador) entre la vista (el formulario HTML)
 * y la API REST en Python (el Modelo/Servidor).
 */

// 1. Recogemos los datos enviados por el usuario mediante el método POST
$usuario = $_POST['usuario'];
$password = $_POST['password'];

// 2. Preparamos los datos en formato array para convertirlos a JSON
$datos_login = [
    'nombre_usuario' => $usuario,
    'contrasena' => $password
];
$json_data = json_encode($datos_login);

// 3. Definimos la URL de nuestra API REST en Python (Flask)
$url = "http://127.0.0.1:5000/auth/login";

// 4. Iniciamos cURL (una librería de PHP para hacer peticiones HTTP a otros servidores)
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Queremos que nos devuelva la respuesta, no imprimirla de golpe
curl_setopt($ch, CURLOPT_POST, true);           // Especificamos que es una petición POST
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data); // Adjuntamos los datos en formato JSON
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'            // Le decimos a la API que le estamos mandando un JSON
]);

// 5. Ejecutamos la petición y capturamos la respuesta de la API
$respuesta = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Obtenemos el código de estado (200 = OK, 401 = Error)
curl_close($ch);

// 6. Lógica de sesión y redirección basada en la respuesta del servidor
if ($http_code == 200) {
    // Si la API nos da un 200 (OK), decodificamos el JSON de respuesta
    $respuesta_json = json_decode($respuesta, true);
    
    // Iniciamos la sesión de PHP de forma segura
    session_start();
    
    // Guardamos los datos críticos en las variables de sesión del servidor
    $_SESSION['token'] = $respuesta_json['token'];   // Token JWT para mantener la seguridad
    $_SESSION['rol'] = $respuesta_json['rol'];       // Rol (admin, profesor, alumno) para control de accesos
    $_SESSION['nombre_usuario'] = $respuesta_json['usuario'];
    
    // Redirigimos al usuario al panel principal (Front Controller se encarga de mostrar la vista real)
    header("Location: ../dashboard.php");
    exit();
} else {
    // Si la contraseña es incorrecta o hay error, devolvemos al index con una alerta
    header("Location: ../index.php?error=login");
    exit();
}
?>