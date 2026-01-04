<?php

require_once __DIR__ . "/../../includes/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
    exit;
}

// Obtener datos desde JSON o POST tradicional
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$idInput = $data['idjunta_directiva'] ?? ($_POST['idjunta_directiva'] ?? null);

// Validar ID
if (empty($idInput)) {
    echo json_encode(["status" => "error", "message" => "No se ha proporcionado ningún ID para eliminar."]);
    exit;
}

// Convertir a array si es un solo ID
$ids = is_array($idInput) ? $idInput : [$idInput];

// Iniciar transacción
$conn->begin_transaction();
try {
    $stmt = $conn->prepare("DELETE FROM junta_directiva WHERE idjunta_directiva = ?");

    foreach ($ids as $id) {
        $id = intval($id);
        if ($id <= 0) {
            throw new Exception("ID inválido: $id");
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception("No se encontró una junta directiva con ID $id o ya fue eliminada.");
        }
    }

    $conn->commit();
    echo json_encode(["status" => "success", "message" => "Junta(s) directiva(s) eliminada(s) correctamente."]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
