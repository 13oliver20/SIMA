<?php 
require_once(__DIR__ . "/../../includes/conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    // Verificar que el ID del socio existe
    $query_check = "SELECT 1 FROM socio WHERE idsocio = ?";
    $stmt_check = $conn->prepare($query_check);
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows == 0) {
        echo json_encode(["success" => false, "message" => "Socio no encontrado"]);
        $stmt_check->close();
        exit;
    }
    $stmt_check->close();

    // Eliminar el socio
    $query = "DELETE FROM socio WHERE idsocio = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Socio eliminado correctamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al eliminar el socio"]);
    }

    $stmt->close();
    $conn->close();
}
?>
