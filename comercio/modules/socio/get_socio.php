<?php 
require_once(__DIR__ . "/../../includes/conexion.php");

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Consulta para obtener los datos del socio
    $query = "SELECT * FROM socio WHERE idsocio = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $socio = $result->fetch_assoc();

    if ($socio) {
        echo json_encode([
            'success' => true,
            'data' => $socio
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Socio no encontrado']);
    }
}
?>
