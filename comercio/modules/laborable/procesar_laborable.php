<?php
require_once(__DIR__ . "/../../includes/conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $grupo_idgrupo = $_POST['grupo_idgrupo'];
    $dias_laborables = isset($_POST['dias_laborables']) ? $_POST['dias_laborables'] : [];

    // Comprobar si se enviaron días laborables
    if (empty($dias_laborables)) {
        echo json_encode(["status" => "error", "message" => "Por favor selecciona al menos un día laborable."]);
        exit;
    }

    // Validar si ya existen los días laborables asignados para el grupo
    foreach ($dias_laborables as $dia) {
        // Consulta para comprobar si el día ya está asignado a este grupo
        $query = "SELECT COUNT(*) FROM dia_laborable_has_grupo WHERE grupo_idgrupo = ? AND dia_laborable_iddia_laborable = (SELECT iddia_laborable FROM dia_laborable WHERE dia = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $grupo_idgrupo, $dia);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            // Si ya está asignado, retornar error
            echo json_encode(["status" => "error", "message" => "El día $dia ya está asignado a este grupo."]);
            exit;
        }
    }

    // Si no existe, registrar los días laborables
    foreach ($dias_laborables as $dia) {
        // Obtener el ID del día laborable
        $query = "SELECT iddia_laborable FROM dia_laborable WHERE dia = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $dia);
        $stmt->execute();
        $stmt->bind_result($iddia_laborable);
        $stmt->fetch();
        $stmt->close();

        if ($iddia_laborable) {
            // Insertar la asignación en la tabla dia_laborable_has_grupo
            $query_insert = "INSERT INTO dia_laborable_has_grupo (dia_laborable_iddia_laborable, grupo_idgrupo) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($query_insert);
            $stmt_insert->bind_param("ii", $iddia_laborable, $grupo_idgrupo);
            $stmt_insert->execute();
            $stmt_insert->close();
        } else {
            // Si no se encuentra el día, manejar el error (opcional)
            echo json_encode(["status" => "error", "message" => "No se encontró el día $dia en la base de datos."]);
            exit;
        }
    }

    // Retornar éxito
    echo json_encode(["status" => "success", "message" => "Días laborables registrados con éxito para el grupo."]);
} else {
    // Si el método no es POST
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
?>
