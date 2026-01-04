<?php
require_once(__DIR__ . "/../../includes/conexion.php");

if (!$conn) {
    die(json_encode(["error" => "Error de conexión a la base de datos."]));
}

if (!isset($_GET['dni'])) {
    die(json_encode(["error" => "DNI no proporcionado."]));
}

$dni = trim($_GET['dni']);

// Buscar socio
$query = "SELECT idsocio, nombre, apellido_pat, apellido_mat, genero, departamento, provincia, distrito FROM socio WHERE dni = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $dni);
$stmt->execute();
$result = $stmt->get_result();
$socio = $result->fetch_assoc();

if (!$socio) {
    die(json_encode(["error" => "No se encontró un socio con ese DNI."]));
}

// Contar las asociaciones del socio
$query_asociaciones = "SELECT COUNT(*) as total_asociaciones FROM socio_asociacion WHERE socio_idsocio = ?";
$stmt_asociaciones = $conn->prepare($query_asociaciones);
$stmt_asociaciones->bind_param("i", $socio['idsocio']);
$stmt_asociaciones->execute();
$result_asociaciones = $stmt_asociaciones->get_result();
$row = $result_asociaciones->fetch_assoc();
$total_asociaciones = $row['total_asociaciones'];

$socio['total_asociaciones'] = $total_asociaciones;

// Devolver la respuesta en JSON
echo json_encode($socio);
?>
