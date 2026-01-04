<?php
// Configuración de sesión antes de iniciarla
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1); // Para que la cookie no sea accesible vía JavaScript

session_start();

// Validación de usuario
if (!isset($_SESSION['usuario_id'], $_SESSION['usuario_correo'])) {
    header("Location: login.php");
    exit();
}

// Evitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Validar user-agent
if (isset($_SESSION['user_agent'])) {
    if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
}
