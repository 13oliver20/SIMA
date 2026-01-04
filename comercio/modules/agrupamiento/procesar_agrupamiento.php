<?php
include '../../includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $cod_etiqueta = trim($_POST["cod_etiqueta"]);
    $nom_agrupamiento = trim($_POST["nom_agrupamiento"]);

    // Validar que ambos campos no estén vacíos
    if (!empty($cod_etiqueta) && !empty($nom_agrupamiento)) {
        // Verificar si el código de agrupamiento ya existe
        $sql_check = "SELECT COUNT(*) FROM agrupamiento WHERE cod_etiqueta = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $cod_etiqueta);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($count > 0) {
            // Si el código ya existe, responder con un mensaje de advertencia
            echo json_encode(["status" => "warning", "message" => "El código de agrupamiento ya se encuentra registrado. Asigne otro código."]);
        } else {
            // Preparar la consulta para insertar los datos
            $sql = "INSERT INTO agrupamiento (cod_etiqueta, nom_agrupamiento) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $cod_etiqueta, $nom_agrupamiento);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Responder con éxito si se registró correctamente
                echo json_encode(["status" => "success", "message" => "Agrupamiento registrado exitosamente."]);
            } else {
                // Responder con error si hubo un problema con la inserción
                echo json_encode(["status" => "danger", "message" => "Error al registrar el agrupamiento."]);
            }

            // Cerrar la sentencia
            $stmt->close();
        }
    } else {
        // Si algún campo está vacío, devolver advertencia
        echo json_encode(["status" => "warning", "message" => "Todos los campos son obligatorios."]);
    }

    // Cerrar la conexión
    $conn->close();
} else {
    // Si no es una solicitud POST, devolver error
    echo json_encode(["status" => "danger", "message" => "Acceso no permitido."]);
}
?>
