<?php
// Verificar si los archivos existen antes de requerirlos
require_once(__DIR__ . "/../../includes/conexion.php");

// Verificar conexión
if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo conectar a la base de datos.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger los datos del formulario
    $num_resolucion = trim($_POST['num_resolucion']);
    $fecha_emision = $_POST['fecha_emision'];
    $grupo_id = $_POST['grupo_id'];
    $archivo_gdh = $_FILES['archivo_gdh'];

    // Validar que la fecha no sea futura
    if (!$fecha_emision || strtotime($fecha_emision) > time()) {
        echo json_encode(['status' => 'error', 'message' => 'Fecha de emisión inválida']);
        exit();
    }

    // Verificar si el número de resolución ya existe en cualquier grupo
    $queryCheck = "SELECT grupo_idgrupo FROM resolucion_gdh WHERE num_resolucion = ?";
    $stmtCheck = $conn->prepare($queryCheck);
    $stmtCheck->bind_param("s", $num_resolucion);
    $stmtCheck->execute();
    $stmtCheck->bind_result($existingGroupId);
    $stmtCheck->fetch();
    $stmtCheck->close();

    // Si el número de resolución existe y pertenece a otro grupo
    if ($existingGroupId && $existingGroupId != $grupo_id) {
        echo json_encode(['status' => 'error', 'message' => 'El número de resolución ya está asignado a otro grupo.']);
        exit();
    }

    // Verificar que se haya subido un archivo y que sea PDF
    if (isset($archivo_gdh) && $archivo_gdh['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $archivo_gdh['tmp_name'];
        $fileName = $archivo_gdh['name'];
        $fileSize = $archivo_gdh['size'];
        $fileType = $archivo_gdh['type'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validar extensión del archivo
        $allowedExtensions = ['pdf'];
        if (!in_array($fileExt, $allowedExtensions)) {
            echo json_encode(['status' => 'error', 'message' => 'El archivo debe ser un PDF']);
            exit();
        }

        // Validar tamaño del archivo (máximo 8MB)
        if ($fileSize > 8 * 1024 * 1024) {
            echo json_encode(['status' => 'error', 'message' => 'El archivo no debe superar los 8MB.']);
            exit();
        }

        // Evitar archivos duplicados renombrándolos con timestamp
        $nuevoNombre = time() . "_" . $fileName;
        $ruta_destino = "../../uploads/resolucion/" . $nuevoNombre;

        // Mover el archivo al directorio de destino
        if (move_uploaded_file($fileTmpPath, $ruta_destino)) {
            // Preparar la consulta con parámetros para evitar inyección SQL
            $query = "INSERT INTO resolucion_gdh (num_resolucion, fecha_emision, archivo_gdh, grupo_idgrupo) 
                      VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssi", $num_resolucion, $fecha_emision, $nuevoNombre, $grupo_id);

            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                echo json_encode(['status' => 'success', 'message' => 'Registro guardado correctamente.']);
                exit();
            } else {
                $stmt->close();
                $conn->close();
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar el registro: ' . $conn->error]);
                exit();
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al subir el archivo']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Debe subir un archivo PDF']);
        exit();
    }
}

// Cerrar conexión
$conn->close();
?>
