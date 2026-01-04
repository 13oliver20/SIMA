<?php
require_once(__DIR__ . "/../../includes/conexion.php");

// Responder en JSON
header('Content-Type: application/json');

// Verificar conexión
if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Error en la conexión a la base de datos."]);
    exit;
}

// Verificar método POST y que se reciba el id
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['padron_id']) || empty($_POST['padron_id'])) {
        echo json_encode(["status" => "error", "message" => "ID del padrón no recibido."]);
        exit;
    }

    $padron_id = intval($_POST['padron_id']);

    // Primero obtener el nombre del archivo para eliminar el archivo físico
    $queryArchivo = "SELECT archivo_padron FROM padron_socios WHERE idpadron_socios = ?";
    $stmtArchivo = $conn->prepare($queryArchivo);
    $stmtArchivo->bind_param("i", $padron_id);
    $stmtArchivo->execute();
    $stmtArchivo->bind_result($archivo_padron);
    if (!$stmtArchivo->fetch()) {
        // No existe el padrón
        echo json_encode(["status" => "error", "message" => "No se encontró el padrón especificado."]);
        $stmtArchivo->close();
        exit;
    }
    $stmtArchivo->close();

    // Ruta del archivo físico
    $rutaArchivo = __DIR__ . "/../../uploads/padron/" . $archivo_padron;

    // Eliminar registro de la base de datos
    $queryEliminar = "DELETE FROM padron_socios WHERE idpadron_socios = ?";
    $stmtEliminar = $conn->prepare($queryEliminar);
    $stmtEliminar->bind_param("i", $padron_id);

    if ($stmtEliminar->execute()) {
        $stmtEliminar->close();

        // Intentar eliminar archivo físico, si existe
        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }

        echo json_encode(["status" => "success", "message" => "Padrón eliminado correctamente."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al eliminar el padrón: " . $conn->error]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}

$conn->close();
?>
