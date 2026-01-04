<?php

require_once(__DIR__ . "/../../includes/conexion.php");

// Leer JSON de la entrada POST
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

try {
    // Preparar la consulta para insertar o actualizar
    $stmt = $pdo->prepare("
        INSERT INTO verificacion_asociados 
          (salida_campo_idsalida, socio_asociacion_socio_idsocio, socio_asociacion_grupo_idgrupo, verificacion, fecha_verificacion) 
        VALUES (:salida, :socio, :grupo, :verificacion, :fecha)
        ON DUPLICATE KEY UPDATE 
          verificacion = VALUES(verificacion),
          fecha_verificacion = VALUES(fecha_verificacion)
    ");

    foreach ($data as $registro) {
        $socio_id = $registro['socio_id'];
        $grupo_id = $registro['grupo_id'];

        foreach ($registro['asistencias'] as $asis) {
            $salida_id = $asis['salida_campo_id'];
            $verificacion = $asis['verificacion'];
            $fecha = $asis['fecha'] ?: null;

            $stmt->execute([
                ':salida' => $salida_id,
                ':socio' => $socio_id,
                ':grupo' => $grupo_id,
                ':verificacion' => $verificacion,
                ':fecha' => $fecha
            ]);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Asistencias registradas correctamente']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
