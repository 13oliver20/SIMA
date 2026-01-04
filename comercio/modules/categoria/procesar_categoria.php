<?php
include '../../includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo = trim($_POST["tipo"]);

    // Convertir a mayúsculas para evitar diferencias por caso
    $tipo = strtoupper($tipo);

    // Eliminar posibles caracteres no deseados, por ejemplo, los acentos
    $tipo = preg_replace('/[áéíóúÁÉÍÓÚ]/', '', $tipo); // Eliminar acentos

    // Palabras clave que pueden generar duplicidades como "S" o "ES"
    $palabras_a_eliminar = ["S", "ES", "AS", "AM", "OS", "O", "E"];
    foreach ($palabras_a_eliminar as $palabra) {
        $tipo = preg_replace("/\b$palabra\b/", '', $tipo);
    }

    // Verificar si la categoría ya existe con comparación por distancia Levenshtein
    $sql_check = "SELECT tipo FROM categoria";
    $stmt = $conn->prepare($sql_check);
    $stmt->execute();
    $stmt->bind_result($categoria);
    
    $encontrado = false;
    while ($stmt->fetch()) {
        // Compara la distancia de Levenshtein entre el nombre ingresado y el nombre de la categoría existente
        $distancia = levenshtein($tipo, strtoupper($categoria)); // Compara sin distinción de mayúsculas

        // Si la distancia de Levenshtein es pequeña, consideramos que es una coincidencia (por ejemplo, <= 2)
        if ($distancia <= 2) {
            $encontrado = true;
            break;
        }
    }

    $stmt->close();

    if ($encontrado) {
        // Si la categoría es similar, mostrar un mensaje de advertencia
        echo json_encode(["status" => "warning", "message" => "Ya existe una categoría similar."]);
    } else {
        // Si no existe, insertar la categoría
        $sql = "INSERT INTO categoria (tipo) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $tipo);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Categoría registrada exitosamente."]);
        } else {
            echo json_encode(["status" => "danger", "message" => "Error al registrar la categoría."]);
        }

        $stmt->close();
    }

    $conn->close();
} else {
    echo json_encode(["status" => "danger", "message" => "Acceso no permitido."]);
}
?>
