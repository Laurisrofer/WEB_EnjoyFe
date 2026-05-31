<?php
// Iniciamos la sesión para poder destruirla
session_start();

// Eliminamos todas las variables de sesión
$_SESSION = array();

// Si se usaban cookies de sesión, las eliminamos
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruimos la sesión
session_destroy();

// Redirigimos al usuario al login
header("Location: ../index.php");
exit();
?>