<?php
require_once(__DIR__ . "/../../includes/conexion.php");

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $sqlBuscar = "SELECT archivo_acta FROM acta_constitucion WHERE idacta_constitucion = ?";
    $stmtBuscar = $conn->prepare($sqlBuscar);
    $stmtBuscar->bind_param("i", $id);
    $stmtBuscar->execute();
    $result = $stmtBuscar->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $archivo = $row['archivo_acta'];

        $rutaArchivo = __DIR__ . '/../../uploads/constitucion/' . $archivo;

        if ($archivo && file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }

        $sql = "DELETE FROM acta_constitucion WHERE idacta_constitucion = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
        $stmt->close();
    } else {
        echo 'not_found';
    }
} else {
    echo 'invalid';
}
?>
