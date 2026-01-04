<?php
// Habilitar la visualización de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir la conexión
include __DIR__ . "/../../includes/conexion.php";

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    if (!$conn) {
        echo json_encode([
            "success" => false,
            "message" => "Error de conexión a la base de datos"
        ]);
        exit;
    }

    // Captura de datos
    $dni = trim($_POST['dni'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido_pat = trim($_POST['apellido_pat'] ?? '');
    $apellido_mat = trim($_POST['apellido_mat'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $departamento = trim($_POST['departamento'] ?? '');
    $provincia = trim($_POST['provincia'] ?? '');
    $distrito = trim($_POST['distrito'] ?? '');

    // Validaciones básicas
    if (empty($dni) || empty($nombre) || empty($apellido_pat) || empty($genero) || empty($departamento) || empty($provincia)) {
        echo json_encode([
            "success" => false,
            "message" => "Todos los campos obligatorios deben llenarse"
        ]);
        exit;
    }

    if (!preg_match('/^\d{8}$/', $dni)) {
        echo json_encode([
            "success" => false,
            "message" => "El DNI debe tener 8 dígitos"
        ]);
        exit;
    }

    // Validar si el DNI ya existe
    $stmt_check = $conn->prepare("SELECT 1 FROM socio WHERE dni = ?");
    $stmt_check->bind_param("s", $dni);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo json_encode([
            "success" => false,
            "message" => "El DNI ya está registrado"
        ]);
        $stmt_check->close();
        exit;
    }
    $stmt_check->close();

    // Insertar en la base de datos
    $stmt_insert = $conn->prepare("INSERT INTO socio (dni, nombre, apellido_pat, apellido_mat, genero, departamento, provincia, distrito) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt_insert) {
        $stmt_insert->bind_param("ssssssss", $dni, $nombre, $apellido_pat, $apellido_mat, $genero, $departamento, $provincia, $distrito);

        if ($stmt_insert->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Socio registrado correctamente"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Error al registrar el socio: " . $stmt_insert->error
            ]);
        }

        $stmt_insert->close();
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Error en la preparación de la consulta"
        ]);
    }

    $conn->close();
}
?>
