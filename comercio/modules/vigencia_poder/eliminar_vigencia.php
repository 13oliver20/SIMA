<?php
require_once(__DIR__ . "/../../includes/conexion.php");

if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Buscar el archivo para eliminar del servidor
    $sqlBuscar = "SELECT archivo_vigencia FROM vigencia_poder WHERE idvigencia_poder = ?";
    $stmtBuscar = $conn->prepare($sqlBuscar);
    $stmtBuscar->bind_param("i", $id);
    $stmtBuscar->execute();
    $result = $stmtBuscar->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $archivo = $row['archivo_vigencia'];

        // Eliminar archivo si existe
        $rutaArchivo = __DIR__ . "/../../uploads/vigencia/" . $archivo;
        if ($archivo && file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }

        // Eliminar registro BD
        $sqlEliminar = "DELETE FROM vigencia_poder WHERE idvigencia_poder = ?";
        $stmtEliminar = $conn->prepare($sqlEliminar);
        $stmtEliminar->bind_param("i", $id);

        if ($stmtEliminar->execute()) {
            if ($stmtEliminar->affected_rows > 0) {
                echo 'success';
            } else {
                echo 'not_found'; // No se eliminó ningún registro
            }
        } else {
            echo 'error: ' . $stmtEliminar->error; // Mostrar error detallado
        }
        $stmtEliminar->close();
    } else {
        echo 'not_found'; // Registro no encontrado
    }

    $stmtBuscar->close();
} else {
    echo 'invalid'; // No se envió id
}

$conn->close();
?>
