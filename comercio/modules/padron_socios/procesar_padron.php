<?php
require_once(__DIR__ . "/../../includes/conexion.php");

// Responder en JSON si se usa AJAX
header('Content-Type: application/json');

// Verificar conexión
if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Error en la conexión a la base de datos."]);
    exit;
}

// Verificar si se recibió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar que se haya seleccionado un grupo
    if (!isset($_POST['grupo_id']) || empty($_POST['grupo_id'])) {
        echo json_encode(["status" => "error", "message" => "Debe seleccionar un grupo."]);
        exit;
    }

    $grupo_id = $_POST['grupo_id'];

    // Verificar si el grupo ya tiene un padrón asignado
    $queryCheckPadron = "SELECT COUNT(*) FROM padron_socios WHERE grupo_idgrupo = ?";
    $stmtCheck = $conn->prepare($queryCheckPadron);
    $stmtCheck->bind_param("i", $grupo_id);
    $stmtCheck->execute();
    $stmtCheck->bind_result($count);
    $stmtCheck->fetch();
    $stmtCheck->close();

    if ($count > 0) {
        echo json_encode(["status" => "error", "message" => "Este grupo ya tiene un padrón asignado."]);
        exit;
    }

    // Verificar si se subió un archivo
    if (!isset($_FILES['archivo_padron']) || $_FILES['archivo_padron']['error'] !== 0) {
        echo json_encode(["status" => "error", "message" => "Debe subir un archivo válido."]);
        exit;
    }

    $archivo_padron = $_FILES['archivo_padron'];
    $archivo_nombre = basename($archivo_padron['name']);
    $archivo_tmp = $archivo_padron['tmp_name'];
    $archivo_ext = strtolower(pathinfo($archivo_nombre, PATHINFO_EXTENSION));

    // Validar que sea un archivo PDF
    if ($archivo_ext !== "pdf") {
        echo json_encode(["status" => "error", "message" => "Solo se permiten archivos PDF."]);
        exit;
    }

    // Validar el tipo MIME del archivo (para seguridad adicional)
    $mime_type = mime_content_type($archivo_tmp);
    if ($mime_type !== "application/pdf") {
        echo json_encode(["status" => "error", "message" => "El archivo debe ser un PDF."]);
        exit;
    }

    // Generar un nombre único para evitar sobrescribir archivos
    $archivo_nombre_nuevo = uniqid() . "_" . $archivo_nombre;
    $ruta_destino = "../../uploads/padron/" . $archivo_nombre_nuevo;

    // Intentar mover el archivo
    if (move_uploaded_file($archivo_tmp, $ruta_destino)) {
        // Usamos prepared statements para evitar SQL Injection
        $query = "INSERT INTO `padron_socios` (archivo_padron, grupo_idgrupo) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $archivo_nombre_nuevo, $grupo_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Padrón registrado correctamente."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error en la base de datos: " . $conn->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Error al subir el archivo."]);
    }
}

$conn->close();
?>
