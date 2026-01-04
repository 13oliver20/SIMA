<?php
require_once(__DIR__ . "/../../includes/conexion.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        echo json_encode(['success' => false, 'error' => 'ID de cargo no proporcionado.']);
        exit;
    }

    $id = intval($_POST['id']);

    $stmt = $conn->prepare("DELETE FROM cargo WHERE idcargo = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al ejecutar la eliminación.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al preparar la consulta.']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Método de solicitud inválido.']);
}
?>
