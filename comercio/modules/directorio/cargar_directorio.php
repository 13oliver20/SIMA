<?php
require_once(__DIR__ . "/../../includes/conexion.php");

// Verificar conexión
if (!$conn) {
    die(json_encode(["error" => "No se pudo conectar a la base de datos."]));
}

// Variables para la paginación y búsqueda
$searchValue = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';
$limit = $_POST['length'] ?? 10;
$offset = $_POST['start'] ?? 0;

// Condición de búsqueda
$where = " WHERE 1=1 ";
if (!empty($searchValue)) {
    $where .= " AND (s.dni LIKE '%$searchValue%' 
                     OR s.nombre LIKE '%$searchValue%' 
                     OR s.apellido_pat LIKE '%$searchValue%' 
                     OR s.apellido_mat LIKE '%$searchValue%' 
                     OR c.tipo_cargo LIKE '%$searchValue%' 
                     OR g.nombre_grupo LIKE '%$searchValue%'
                     OR a.cod_etiqueta LIKE '%$searchValue%')";
}

// Consulta de datos paginados con JOIN en agrupamiento
$query = "SELECT 
            j.idjunta_directiva, 
            j.fecha_inicio, 
            j.fecha_fin, 
            g.nombre_grupo, 
            s.dni AS dni_socio, 
            s.nombre AS nombre_socio,
            s.apellido_pat AS apellido_paterno, 
            s.apellido_mat AS apellido_materno, 
            c.tipo_cargo AS nombre_cargo, 
            j.celular,
            a.cod_etiqueta AS etiqueta_grupo  -- Nueva columna de agrupamiento
          FROM junta_directiva j
          INNER JOIN socio_asociacion sa 
              ON j.socio_asociacion_socio_idsocio = sa.socio_idsocio 
              AND j.socio_asociacion_grupo_idgrupo = sa.grupo_idgrupo
          INNER JOIN grupo g ON sa.grupo_idgrupo = g.idgrupo
          INNER JOIN socio s ON sa.socio_idsocio = s.idsocio
          INNER JOIN cargo c ON j.cargo_idcargo = c.idcargo
          INNER JOIN agrupamiento a ON g.agrupamiento_idagrupamiento = a.idagrupamiento -- Relación con agrupamiento
          $where
          ORDER BY s.nombre, g.nombre_grupo, c.tipo_cargo
          LIMIT $limit OFFSET $offset";

$result = $conn->query($query);

// Consulta total de registros
$totalRecordsQuery = "SELECT COUNT(*) AS total FROM junta_directiva";
$totalRecordsResult = $conn->query($totalRecordsQuery);
$totalRecords = $totalRecordsResult->fetch_assoc()['total'];

// Consulta total de registros filtrados
$totalFilteredQuery = "SELECT COUNT(*) AS total FROM junta_directiva j
                        INNER JOIN socio_asociacion sa 
                          ON j.socio_asociacion_socio_idsocio = sa.socio_idsocio 
                          AND j.socio_asociacion_grupo_idgrupo = sa.grupo_idgrupo
                        INNER JOIN grupo g ON sa.grupo_idgrupo = g.idgrupo
                        INNER JOIN socio s ON sa.socio_idsocio = s.idsocio
                        INNER JOIN cargo c ON j.cargo_idcargo = c.idcargo
                        INNER JOIN agrupamiento a ON g.agrupamiento_idagrupamiento = a.idagrupamiento
                        $where";

$totalFilteredResult = $conn->query($totalFilteredQuery);
$totalFiltered = $totalFilteredResult->fetch_assoc()['total'];

$data = [];
$counter = $offset + 1;
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "num" => $counter++,
        "dni_socio" => $row['dni_socio'],
        "nombre_socio" => $row['nombre_socio'] . ' ' . $row['apellido_paterno'] . ' ' . $row['apellido_materno'],
        "etiqueta_grupo" => $row['etiqueta_grupo'],
        "nombre_grupo" => $row['nombre_grupo'],
        "nombre_cargo" => $row['nombre_cargo'],
        "celular" => $row['celular'],
        "acciones" => '<button class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>'
    ];
}

echo json_encode(["draw" => intval($_POST['draw'] ?? 1), "recordsTotal" => $totalRecords, "recordsFiltered" => $totalFiltered, "data" => $data]);
?>
