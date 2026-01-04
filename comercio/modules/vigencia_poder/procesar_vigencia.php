<?php
header('Content-Type: application/json');
require_once(__DIR__ . "/../../includes/conexion.php");

if (!$conn) {
    echo json_encode(["status" => "error", "message" => "No se pudo conectar a la base de datos."]);
    exit;
}

$partida_registral = $_POST['partida_registral'] ?? null;
$grupo_id = $_POST['grupo_id'] ?? null;
$archivo_vigencia = $_FILES['archivo_vigencia'] ?? null;

// Validación de campos obligatorios
if (!$partida_registral || !$grupo_id || !$archivo_vigencia) {
    echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios."]);
    exit;
}

// Validación del ID de grupo
if (!filter_var($grupo_id, FILTER_VALIDATE_INT)) {
    echo json_encode(["status" => "error", "message" => "ID de grupo inválido."]);
    exit;
}

// Validación del archivo
if ($archivo_vigencia['error'] !== 0) {
    echo json_encode(["status" => "error", "message" => "Error al subir el archivo (Código: " . $archivo_vigencia['error'] . ")"]);
    exit;
}

// Validación MIME real del archivo
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $archivo_vigencia['tmp_name']);
finfo_close($finfo);

if ($mime !== 'application/pdf') {
    echo json_encode(["status" => "error", "message" => "El archivo debe ser un PDF."]);
    exit;
}

// Validación de tamaño (máximo 8MB)
$maxSize = 8 * 1024 * 1024;
if ($archivo_vigencia['size'] > $maxSize) {
    echo json_encode(["status" => "error", "message" => "El archivo no debe superar los 8MB."]);
    exit;
}

// Verificar si el grupo ya tiene un registro
$checkGrupo = $conn->prepare("SELECT 1 FROM vigencia_poder WHERE grupo_idgrupo = ?");
$checkGrupo->bind_param("i", $grupo_id);
$checkGrupo->execute();
$checkGrupo->store_result();

if ($checkGrupo->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Este grupo ya tiene una partida y un archivo registrado."]);
    $checkGrupo->close();
    exit;
}
$checkGrupo->close();

// Verificar si la partida registral ya existe
$checkPartida = $conn->prepare("SELECT 1 FROM vigencia_poder WHERE partida_registral = ?");
$checkPartida->bind_param("s", $partida_registral);
$checkPartida->execute();
$checkPartida->store_result();

if ($checkPartida->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Esta partida registral ya está registrada."]);
    $checkPartida->close();
    exit;
}
$checkPartida->close();

// Procesar subida del archivo
$nombre_sanitizado = preg_replace("/[^a-zA-Z0-9\._-]/", "_", basename($archivo_vigencia['name']));
$archivo_nombre = time() . "_" . $nombre_sanitizado;
$uploadDir = "../../uploads/vigencia/";
$ruta_destino = $uploadDir . $archivo_nombre;

// Crear el directorio si no existe
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        echo json_encode(["status" => "error", "message" => "No se pudo crear el directorio de destino."]);
        exit;
    }
}

// Mover el archivo
if (move_uploaded_file($archivo_vigencia['tmp_name'], $ruta_destino)) {
    $query = "INSERT INTO vigencia_poder (partida_registral, archivo_vigencia, grupo_idgrupo) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("ssi", $partida_registral, $archivo_nombre, $grupo_id);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Registro guardado con éxito."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al guardar: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Error al preparar la consulta SQL."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Error al mover el archivo al servidor."]);
}

$conn->close();
?>