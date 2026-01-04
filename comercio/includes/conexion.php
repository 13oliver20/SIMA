<?php
// Incluir el adaptador PostgreSQL
require_once 'PostgresAdapter.php';

// Leer variables de entorno (Railway) con fallback a valores locales
$host = getenv('DB_HOST') ?: 'db.ejtmcckwcfdgrqmdruax.supabase.co';
$port = getenv('DB_PORT') ?: '5432';
$user = getenv('DB_USER') ?: 'postgres';
$password = getenv('DB_PASSWORD') ?: '6enniudV12@';
$database = getenv('DB_NAME') ?: 'postgres';

// Crear conexión usando PostgreSQL directo
$conn = new PostgresAdapter($host, $user, $password, $database, $port);

// Comprobar errores
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>