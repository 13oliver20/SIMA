<?php
// Incluir el adaptador PostgreSQL
require_once 'PostgresAdapter.php';

// Datos de conexión a Supabase (PostgreSQL directo)
$host = 'db.ejtmcckwcfdgrqmdruax.supabase.co';
$port = '5432';  // Puerto estándar PostgreSQL
$user = 'postgres';
$password = '6enniudV12@';  // Tu contraseña de Supabase
$database = 'postgres';

// Crear conexión usando PostgreSQL directo
$conn = new PostgresAdapter($host, $user, $password, $database, $port);

// Comprobar errores
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>