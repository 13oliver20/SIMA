<?php
require_once(__DIR__ . "/../../includes/conexion.php");

// Habilitar errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verificar conexión
if (!$conn) {
    die(json_encode(['status' => 'error', 'message' => 'No se pudo conectar a la base de datos.']));
}

// Recibir datos POST
$id = $_POST['idgrupo'] ?? null;
$nombre = $_POST['nombre_grupo'] ?? '';
$ubicacion = $_POST['ubicacion'] ?? '';
$agrupamiento = $_POST['agrupamiento_id'] ?? '';
$categoria = $_POST['categoria_id'] ?? '';
$estado = $_POST['estado'] ?? '';

// Logs para depurar qué llega
error_log("idgrupo: " . var_export($id, true));
error_log("nombre_grupo: " . var_export($nombre, true));
error_log("ubicacion: " . var_export($ubicacion, true));
error_log("agrupamiento_id: " . var_export($agrupamiento, true));
error_log("categoria_id: " . var_export($categoria, true));
error_log("estado: " . var_export($estado, true));

// Validar campos obligatorios
if (empty($id) || empty($nombre) || empty($ubicacion) || empty($agrupamiento) || empty($categoria) || empty($estado)) {
    echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
    exit;
}

// Preparar consulta para evitar SQL injection
$stmt = $conn->prepare("UPDATE grupo SET nombre_grupo=?, ubicacion=?, agrupamiento_idagrupamiento=?, categoria_idcategoria=?, estado=? WHERE idgrupo=?");
if (!$stmt) {
    error_log("Error en preparación de consulta: " . $conn->error);
    echo json_encode(['status' => 'error', 'message' => 'Error en la preparación de la consulta.']);
    exit;
}

// Enlazar parámetros: nombre(string), ubicacion(string), agrupamiento(int), categoria(int), estado(string), id(int)
$stmt->bind_param("ssiisi", $nombre, $ubicacion, $agrupamiento, $categoria, $estado, $id);

// Ejecutar consulta
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Grupo actualizado correctamente.']);
} else {
    error_log("Error al ejecutar la consulta: " . $stmt->error);
    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el grupo.']);
}

$stmt->close();
$conn->close();
?>
