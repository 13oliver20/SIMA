<?php
require_once(__DIR__ . "/../../includes/conexion.php");

if (!isset($_POST['idgrupo'])) {
    echo json_encode(["status" => "error", "message" => "ID no proporcionado."]);
    exit;
}

$idgrupo = intval($_POST['idgrupo']);

$query = "SELECT * FROM grupo WHERE idgrupo = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $idgrupo);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["status" => "success", "data" => $row]);
} else {
    echo json_encode(["status" => "error", "message" => "Grupo no encontrado."]);
}
?>
