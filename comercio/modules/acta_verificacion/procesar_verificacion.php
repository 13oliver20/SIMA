<?php
header('Content-Type: application/json'); // Para respuesta en JSON
require_once(__DIR__ . "/../../includes/conexion.php");

// Verificar conexión
if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Error: No se pudo conectar a la base de datos."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recuperar los datos del formulario
    $fecha_verificacion = $_POST['fecha_verificacion'] ?? null;
    $grupo_id = $_POST['grupo_id'] ?? null;
    $acta_id = $_POST['acta_id'] ?? null;  // id del acta de verificación
    $archivo_verificacion = $_FILES['archivo_verificacion'] ?? null;

    // Validaciones de los campos
    $errors = [
        empty($fecha_verificacion) => "La fecha de verificación es obligatoria.",
        empty($grupo_id) || empty($acta_id) => "El grupo y el acta de verificación son obligatorios.",
    ];

    foreach ($errors as $condition => $message) {
        if ($condition) {
            echo json_encode(["status" => "error", "message" => $message]);
            exit;
        }
    }

    // Validar archivo de verificación
    if ($archivo_verificacion && $archivo_verificacion['error'] === UPLOAD_ERR_OK) {
        // Validaciones del archivo
        $fileErrors = [
            $archivo_verificacion['type'] !== 'application/pdf' => "El archivo debe ser un PDF.",
            $archivo_verificacion['size'] > 8 * 1024 * 1024 => "El archivo no debe superar los 8MB.",
        ];

        foreach ($fileErrors as $condition => $message) {
            if ($condition) {
                echo json_encode(["status" => "error", "message" => $message]);
                exit;
            }
        }

        // Generar un nombre único para el archivo
        $archivo_nombre = time() . "_" . basename($archivo_verificacion['name']);
        $ruta_destino = "../../uploads/verificacion/" . $archivo_nombre;

        // Crear directorio si no existe
        if (!file_exists("../../uploads/verificacion/") && !mkdir("../../uploads/verificacion/", 0777, true)) {
            echo json_encode(["status" => "error", "message" => "Error al crear el directorio para el archivo."]);
            exit;
        }

        // Mover archivo
        if (!move_uploaded_file($archivo_verificacion['tmp_name'], $ruta_destino)) {
            echo json_encode(["status" => "error", "message" => "Error al mover el archivo."]);
            exit;
        }
    } else {
        $archivo_nombre = null;  // Archivo opcional
    }

    // Verificar si ya existe el acta de verificación para el grupo
    $checkGrupo = $conn->prepare("SELECT grupo_idgrupo FROM acta_verificacion_has_grupo WHERE grupo_idgrupo = ? AND acta_verificacion_idacta_verificacion = ?");
    $checkGrupo->bind_param("ii", $grupo_id, $acta_id);
    $checkGrupo->execute();
    $checkGrupo->store_result();

    if ($checkGrupo->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Este grupo ya tiene un acta de verificación registrada."]);
        $checkGrupo->close();
        exit;
    }
    $checkGrupo->close();

    // Insertar en base de datos
    $query = "INSERT INTO acta_verificacion_has_grupo (grupo_idgrupo, fecha_verificacion, acta_verificacion_idacta_verificacion, archivo_verificacion) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("isss", $grupo_id, $fecha_verificacion, $acta_id, $archivo_nombre);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Acta de verificación registrada correctamente."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al registrar el acta de verificación: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Error en la consulta SQL."]);
    }
}

$conn->close();
?>
