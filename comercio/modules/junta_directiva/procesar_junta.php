<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
include __DIR__ . "/../../includes/conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $grupo = $_POST['grupo'] ?? null;
    $fecha_inicio = $_POST['fecha_inicio'] ?? null;
    $fecha_fin = $_POST['fecha_fin'] ?? null;

    if (empty($grupo)) {
        echo json_encode(["status" => "error", "message" => "Debe seleccionar un grupo."]);
        exit;
    }

    if ($fecha_inicio > $fecha_fin) {
        echo json_encode(["status" => "error", "message" => "La fecha de inicio no puede ser mayor que la fecha de fin."]);
        exit;
    }

    $sociosIngresados = false;
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'dni_') === 0) {
            $sociosIngresados = true;
            break;
        }
    }

    if (!$sociosIngresados) {
        echo json_encode(["status" => "error", "message" => "Debe registrar al menos un socio en la junta directiva."]);
        exit;
    }

    $hoy = date('Y-m-d');
    $estado = ($fecha_inicio <= $hoy && $hoy <= $fecha_fin) ? 'Activo' : 'Inactivo';

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO junta_directiva 
            (socio_asociacion_socio_idsocio, socio_asociacion_grupo_idgrupo, cargo_idcargo, fecha_inicio, fecha_fin, celular, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiissss", $socio_id, $grupo, $cargo, $fecha_inicio, $fecha_fin, $celular, $estado);

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'dni_') === 0) {
                $index = str_replace('dni_', '', $key);
                $dni = trim($value);
                $celular = trim($_POST["celular_$index"] ?? '' );
                $cargo = trim($_POST["cargo_$index"]);

                if (!empty($celular) && !preg_match('/^\d{9}$/', $celular)) {
                    throw new Exception("El número de celular ingresado para el socio con DNI $dni no es válido. Debe tener exactamente 9 dígitos.");
                }

                $celular = empty($celular) ? NULL : $celular;

                $query = "SELECT s.idsocio, sa.grupo_idgrupo 
                          FROM socio s 
                          JOIN socio_asociacion sa ON s.idsocio = sa.socio_idsocio 
                          WHERE s.dni = ? AND sa.grupo_idgrupo = ?";

                $stmtCheck = $conn->prepare($query);
                $stmtCheck->bind_param("si", $dni, $grupo);
                $stmtCheck->execute();
                $result = $stmtCheck->get_result();

                if ($result->num_rows == 0) {
                    throw new Exception("El socio con DNI $dni no está registrado en el sistema o no pertenece al grupo seleccionado.");
                }

                $row = $result->fetch_assoc();
                $socio_id = $row['idsocio'];

                $queryDuplicado = "SELECT idjunta_directiva FROM junta_directiva 
                                   WHERE socio_asociacion_socio_idsocio = ? 
                                   AND socio_asociacion_grupo_idgrupo = ?";
                $stmtDup = $conn->prepare($queryDuplicado);
                $stmtDup->bind_param("ii", $socio_id, $grupo);
                $stmtDup->execute();
                $resultDup = $stmtDup->get_result();

                if ($resultDup->num_rows > 0) {
                    throw new Exception("El socio con DNI $dni ya está registrado en esta junta directiva.");
                }

                // Insertar los datos de la junta
                $stmt->execute();
            }
        }

        $conn->commit();
        echo json_encode(["status" => "success", "message" => "La junta directiva se ha registrado correctamente."]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>
