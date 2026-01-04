<?php
require_once(__DIR__ . "/../../includes/conexion.php");

// Verifica si la conexión es válida
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger y limpiar los datos del formulario
    $etiqueta_grupo = trim($_POST['etiqueta_grupo']);
    $nombre_grupo = trim($_POST['nombre_grupo']);
    $ubicacion = trim($_POST['ubicacion']);
    $agrupamiento_id = $_POST['agrupamiento_id'];
    $categoria_id = $_POST['categoria_id'];
    $estado = $_POST['estado'];

    // Validación básica
    if (
        empty($etiqueta_grupo) || 
        empty($nombre_grupo) || 
        empty($ubicacion) || 
        empty($agrupamiento_id) || 
        empty($categoria_id) || 
        empty($estado)
    ) {
        echo json_encode([
            "status" => "error", 
            "message" => "Todos los campos son obligatorios."
        ]);
        exit();
    }

    try {
        // Preparar la consulta para insertar el nuevo grupo
        $query = "INSERT INTO grupo (
                    etiqueta_grupo, 
                    nombre_grupo, 
                    ubicacion, 
                    agrupamiento_idagrupamiento, 
                    categoria_idcategoria, 
                    estado
                  ) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssiss", 
            $etiqueta_grupo, 
            $nombre_grupo, 
            $ubicacion, 
            $agrupamiento_id, 
            $categoria_id, 
            $estado
        );

        $stmt->execute();

        // Cerrar conexión y liberar recursos
        $stmt->close();
        $conn->close();

        echo json_encode([
            "status" => "success", 
            "message" => "Grupo registrado correctamente."
        ]);
        exit();

    } catch (mysqli_sql_exception $e) {
        $errorMessage = "Ocurrió un error.";

        // Si el error es por clave duplicada (código 1062 en MySQL)
        if ($e->getCode() == 1062) {
            $errorMessage = "El grupo con el código '$etiqueta_grupo' ya se encuentra registrado, no podrá ser registrado.";
        } else {
            $errorMessage = "Error en la base de datos: " . $e->getMessage();
        }

        echo json_encode([
            "status" => "error", 
            "message" => $errorMessage
        ]);
        exit();
    }
} else {
    // Si no es POST, devolver error
    echo json_encode([
        "status" => "error", 
        "message" => "Método de solicitud no válido."
    ]);
    exit();
}
