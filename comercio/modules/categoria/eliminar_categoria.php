<?php
include '../../includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idcategoria = isset($_POST["idcategoria"]) ? intval($_POST["idcategoria"]) : 0;

    if ($idcategoria <= 0) {
        echo json_encode(["status" => "warning", "message" => "ID de categoría inválido o no recibido."]);
        exit;
    }

    $sql = "DELETE FROM categoria WHERE idcategoria = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["status" => "danger", "message" => "Error en la preparación de la consulta: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $idcategoria);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Categoría eliminada exitosamente."]);
    } else {
        echo json_encode(["status" => "danger", "message" => "Error al eliminar la categoría: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "danger", "message" => "Acceso no permitido."]);
}
