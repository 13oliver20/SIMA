<?php
require_once(__DIR__ . "/../../includes/conexion.php");

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM socio_asociacion WHERE socio_idsocio = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false]);
}
?>
