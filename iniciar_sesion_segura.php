<?php
// iniciar_sesion_segura.php

// 1. Obtener parámetros de cookie actuales (ruta, dominio, etc.)
$cookieParams = session_get_cookie_params();

// 2. Configurar parámetros de cookie seguros
//    - 'secure' => true requiere HTTPS para que la cookie viaje cifrada
//      En desarrollo local (sin HTTPS) podrías usar 'secure' => false.
//    - 'httponly' => true impide que JavaScript acceda a la cookie.
//    - 'samesite' => 'Strict' o 'Lax' para prevenir envío cruzado de cookies.
session_set_cookie_params([
    'lifetime' => 0, // La cookie expira al cerrar el navegador
    'path'     => $cookieParams['path'],
    'domain'   => $cookieParams['domain'],
    'secure'   => false,   // Usa 'true' en producción con HTTPS
    'httponly' => true,
    'samesite' => 'Strict'
]);

// 3. Iniciar la sesión
session_start();

// 4. Regenerar el ID de sesión en el primer inicio para prevenir fijación de sesión
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}
/* en etapa de produccion esto debe estar desactivado 
// 5. (Opcional) Verificar la consistencia del user agent para mitigar secuestro de sesión
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
} elseif ($_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
    // Si el user agent difiere, se destruye la sesión y se fuerza a re-loguear
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}*/
?>
