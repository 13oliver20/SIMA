<?php
/**
 * PLANTILLA DE CONFIGURACIÓN DE CONEXIÓN A BASE DE DATOS
 * 
 * INSTRUCCIONES:
 * 1. Copia este archivo y renómbralo a 'conexion.php'
 * 2. Reemplaza los valores de ejemplo con tus credenciales reales
 * 3. NUNCA subas el archivo 'conexion.php' a GitHub
 * 
 * Para producción, usa variables de entorno en lugar de hardcodear las credenciales
 */

require_once 'PostgresAdapter.php';

// Configuración para desarrollo local
// En producción, usa: getenv('DB_HOST')
$host = 'TU_HOST_SUPABASE';  // Ejemplo: db.xxx.supabase.co
$port = '5432';
$user = 'postgres';
$password = 'TU_PASSWORD_AQUI';
$database = 'postgres';

// Crear conexión usando PostgresAdapter
$conn = new PostgresAdapter($host, $port, $user, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>