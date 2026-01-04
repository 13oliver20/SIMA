<?php
include '../../includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    if (!empty($id)) {
        $sql = "DELETE FROM agrupamiento WHERE idagrupamiento = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Agrupamiento eliminado exitosamente."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al eliminar el agrupamiento."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "warning", "message" => "ID no válido."]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "danger", "message" => "Acceso no permitido."]);
}
?>
