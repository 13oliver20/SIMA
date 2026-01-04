<?php
header('Content-Type: application/json');
require_once(__DIR__ . "/../../includes/conexion.php");

if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Error: No se pudo conectar a la base de datos."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha_fundacion = trim($_POST['fecha_fundacion'] ?? '');
    $grupo_id = intval($_POST['grupo_id'] ?? 0);
    $archivo_acta = $_FILES['archivo_acta'] ?? null;

    // Validaciones básicas
    if (empty($fecha_fundacion) || !$grupo_id) {
        echo json_encode(["status" => "error", "message" => "Todos los campos obligatorios deben ser completados."]);
        exit;
    }

    if (!$archivo_acta || $archivo_acta['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["status" => "error", "message" => "Error al subir el archivo."]);
        exit;
    }

    if ($archivo_acta['type'] !== 'application/pdf') {
        echo json_encode(["status" => "error", "message" => "El archivo debe estar en formato PDF."]);
        exit;
    }

    if ($archivo_acta['size'] > 8 * 1024 * 1024) {
    echo json_encode(["status" => "error", "message" => "El archivo no debe superar los 8MB."]);
    exit;
}

    // Verificar si el grupo ya tiene un acta registrada
    $stmtCheck = $conn->prepare("SELECT 1 FROM acta_constitucion WHERE grupo_idgrupo = ?");
    $stmtCheck->bind_param("i", $grupo_id);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Este grupo ya tiene un acta registrada."]);
        $stmtCheck->close();
        exit;
    }
    $stmtCheck->close();

    // Preparar destino y nombre del archivo
    $nombre_archivo = time() . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", basename($archivo_acta['name']));
    $carpeta_destino = __DIR__ . "/../../uploads/constitucion/";

    if (!is_dir($carpeta_destino)) {
        mkdir($carpeta_destino, 0777, true);
    }

    $ruta_destino = $carpeta_destino . $nombre_archivo;

    if (!move_uploaded_file($archivo_acta['tmp_name'], $ruta_destino)) {
        echo json_encode(["status" => "error", "message" => "No se pudo guardar el archivo."]);
        exit;
    }

    // Guardar en la base de datos
    $stmt = $conn->prepare("INSERT INTO acta_constitucion (fecha_fundacion, archivo_acta, grupo_idgrupo) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssi", $fecha_fundacion, $nombre_archivo, $grupo_id);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Acta registrada correctamente."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al registrar el acta: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Error al preparar la consulta."]);
    }
}

$conn->close();
