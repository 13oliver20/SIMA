<?php
require_once(__DIR__ . "/../../includes/conexion.php");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idcargo'], $_POST['tipo_cargo'])) {
    $id = intval($_POST['idcargo']);
    $tipo_cargo = trim($_POST['tipo_cargo']);

    if (empty($tipo_cargo)) {
        echo json_encode(['success' => false, 'error' => 'El campo tipo de cargo es obligatorio.']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE cargo SET tipo_cargo = ? WHERE idcargo = ?");
    $stmt->bind_param("si", $tipo_cargo, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar el cargo.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Solicitud inválida.']);
}
?>
