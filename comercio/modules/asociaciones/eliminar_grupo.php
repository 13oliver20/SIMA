<?php
require_once(__DIR__ . "/../../includes/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idgrupo'])) {
    $idgrupo = intval($_POST['idgrupo']);

    $stmt = $conn->prepare("DELETE FROM grupo WHERE idgrupo = ?");
    $stmt->bind_param("i", $idgrupo);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Grupo eliminado correctamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el grupo.']);
    }

    $stmt->close();
    $conn->close();
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Solicitud no válida.']);
