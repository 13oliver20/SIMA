<?php
require_once(__DIR__ . "/../../includes/conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo_cargo = trim($_POST['tipo_cargo']);

    if (!empty($tipo_cargo)) {
        $stmt = $conn->prepare("INSERT INTO cargo (tipo_cargo) VALUES (?)");
        $stmt->bind_param("s", $tipo_cargo);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al registrar']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Campo tipo_cargo vacío']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}
$conn->close();
?>
