<?php
session_start();
require_once(__DIR__ . "/../../includes/conexion.php");
$conn->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;

// Verificar conexión
if (!$conn) {
    echo json_encode(["status" => "error", "message" => "❌ No se pudo conectar a la base de datos."]);
    exit;
}

// Verificar si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idsocio = trim($_POST['idsocio'] ?? '');
    $idasociacion = trim($_POST['idasociacion'] ?? '');
    $subrubro = trim($_POST['id_rubro'] ?? '');  
    $observacion = trim($_POST['observacion'] ?? '');
    $cod_puesto = trim($_POST['cod_puesto'] ?? '');

    // Validar campos requeridos
    if (empty($idsocio) || empty($idasociacion) || empty($subrubro)) {
        echo json_encode(["status" => "warning", "message" => "Todos los campos requeridos deben completarse."]);
        exit;
    }

    // Verificar cuántos grupos tiene el socio
    $query_verificar = "SELECT COUNT(DISTINCT grupo_idgrupo) AS total FROM socio_asociacion WHERE socio_idsocio = ?";
    $stmt_verificar = $conn->prepare($query_verificar);
    $stmt_verificar->bind_param("i", $idsocio);
    $stmt_verificar->execute();
    $result_verificar = $stmt_verificar->get_result();
    $fila_verificar = $result_verificar->fetch_assoc();
    $stmt_verificar->close();

    if ($fila_verificar['total'] > 0) {
        echo json_encode([
            "status" => "warning",
            "message" => "Este socio ya pertenece a " . $fila_verificar['total'] . " grupo(s). No puede ser asignado a otro."
        ]);
        exit;
    }

    // Insertar el nuevo registro en la base de datos
    $query = "INSERT INTO socio_asociacion (socio_idsocio, grupo_idgrupo, subrubro_segundo_idsubrubro_seg, observacion, cod_puesto) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisss", $idsocio, $idasociacion, $subrubro, $observacion, $cod_puesto);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "El socio ha sido registrado correctamente en el grupo."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al registrar socio: " . $conn->error]);
    }

    $stmt->close();
}

$conn->close();
exit;
?>
