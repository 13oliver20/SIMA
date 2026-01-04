<?php
$host = 'db.ejtmcckwcfdgrqmdruax.supabase.co'; // Host actualizado
$bd = 'postgres';
$port = '6543';  // Puerto de Connection Pooling
$usuario = 'postgres';  // Usuario estándar
$contrasena = '6enniudV12@';  // Contraseña actualizada
$charset = 'utf8';

$dsn = "pgsql:host=$host;port=$port;dbname=$bd;options='--client_encoding=$charset'";
$opciones = [
     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
     $pdo = new PDO($dsn, $usuario, $contrasena, $opciones);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int) $e->getCode());
}
?>