<?php
// componentes/api_client.php

/**
 * Función centralizada para realizar peticiones a la API Flask
 * @param string $endpoint El endpoint de la API (ej. "/usuarios")
 * @param string $metodo El método HTTP ("GET", "POST", "PUT", "DELETE")
 * @param array|null $datos Los datos a enviar en formato array asociativo (para POST/PUT)
 * @return array Array con ['http_code' => int, 'respuesta' => string, 'data' => array|null]
 */
function llamar_api($endpoint, $metodo = 'GET', $datos = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $url_base = "http://127.0.0.1:5000";
    $url = $url_base . $endpoint;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
    
    $headers = ['Content-Type: application/json'];
    
    if (isset($_SESSION['token'])) {
        $headers[] = 'Authorization: Bearer ' . $_SESSION['token'];
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($datos !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
    }
    
    $respuesta = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'http_code' => $http_code,
        'respuesta' => $respuesta,
        'data' => json_decode($respuesta, true)
    ];
}
?>
