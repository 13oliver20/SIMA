<?php
// Verificar si los archivos existen antes de requerirlos
require_once(__DIR__ . "/../../includes/conexion.php");

header("Content-Type: application/json"); // Asegurar salida JSON

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'] ?? null;

    if (!$id) {
        echo json_encode(["status" => "error", "message" => "ID inválido."]);
        exit;
    }

    // Verificar si la resolución existe
    $check = $conn->prepare("SELECT * FROM resolucion_gdh WHERE idresolucion_gdh = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Resolución no encontrada."]);
        exit;
    }

    // Eliminar la resolución
    $stmt = $conn->prepare("DELETE FROM resolucion_gdh WHERE idresolucion_gdh = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Resolución eliminada correctamente."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al eliminar."]);
    }
    exit;
} else {
    echo json_encode(["status" => "error", "message" => "Acceso no permitido."]);
    exit;
}
?>
