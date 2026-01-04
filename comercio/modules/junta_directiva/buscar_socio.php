<?php
include '../../includes/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $dni = $_GET['dni'];
    $grupo_id = $_GET['grupo'];

    if (empty($dni) || empty($grupo_id)) {
        echo json_encode(["success" => false, "error" => "Datos incompletos."]);
        exit;
    }

    // Buscar al socio por DNI
    $sql = "SELECT s.idsocio, s.nombre, s.apellido_pat, s.apellido_mat 
            FROM socio s
            WHERE s.dni = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $idsocio = $row['idsocio'];

        // Verificar si el socio pertenece al grupo utilizando la tabla correcta: socio_asociacion
        $sql_grupo = "SELECT 1 FROM socio_asociacion 
                      WHERE socio_idsocio = ? AND grupo_idgrupo = ?";
        $stmt_grupo = $conn->prepare($sql_grupo);
        $stmt_grupo->bind_param("ii", $idsocio, $grupo_id);
        $stmt_grupo->execute();
        $result_grupo = $stmt_grupo->get_result();

        if ($result_grupo->fetch_assoc()) {
            echo json_encode([
                "success" => true,
                "nombre" => $row['nombre'],
                "apellido" => $row['apellido_pat'] . " " . $row['apellido_mat'],
                "celular" => "" // Se retorna vacío ya que el celular no se encuentra en la tabla socio
            ]);
        } else {
            echo json_encode(["success" => false, "error" => "El socio no pertenece al grupo seleccionado."]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "No se encontró un socio con ese DNI."]);
    }
}
?>
