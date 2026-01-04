<?php
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . "/../../includes/conexion.php");

// Verificar conexión
if (!$conn) {
    echo json_encode(["error" => "No se pudo conectar a la base de datos."]);
    exit;
}

// Variables para la paginación
$searchValue = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';
$limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
$offset = isset($_POST['start']) ? intval($_POST['start']) : 0;

// Condición de búsqueda
$where = " WHERE 1=1 ";
if (!empty($searchValue)) {
    $where .= " AND (s.dni LIKE '%$searchValue%' 
                     OR s.nombre LIKE '%$searchValue%' 
                     OR s.apellido_pat LIKE '%$searchValue%' 
                     OR s.apellido_mat LIKE '%$searchValue%' 
                     OR c.tipo_cargo LIKE '%$searchValue%' 
                     OR g.nombre_grupo LIKE '%$searchValue%')";
}

// Consulta de datos paginados
$query = "SELECT 
            j.idjunta_directiva, 
            j.fecha_inicio, 
            j.fecha_fin, 
            g.nombre_grupo, 
            s.nombre AS nombre_socio,
            s.dni AS dni_socio, 
            s.apellido_pat, 
            s.apellido_mat, 
            c.tipo_cargo AS nombre_cargo, 
            j.celular 
          FROM junta_directiva j
          INNER JOIN socio_asociacion ga 
              ON j.socio_asociacion_socio_idsocio = ga.socio_idsocio 
              AND j.socio_asociacion_grupo_idgrupo = ga.grupo_idgrupo
          INNER JOIN grupo g ON ga.grupo_idgrupo = g.idgrupo
          INNER JOIN socio s ON ga.socio_idsocio = s.idsocio
          INNER JOIN cargo c ON j.cargo_idcargo = c.idcargo
          $where
          ORDER BY g.idgrupo, s.nombre, c.tipo_cargo
          LIMIT $limit OFFSET $offset";

$result = $conn->query($query);

// Consulta total de registros sin filtro
$totalRecordsQuery = "SELECT COUNT(*) AS total FROM junta_directiva";
$totalRecordsResult = $conn->query($totalRecordsQuery);
$totalRecords = $totalRecordsResult->fetch_assoc()['total'];

// Consulta total de registros con filtro
$totalFilteredQuery = "SELECT COUNT(*) AS total FROM junta_directiva j
                        INNER JOIN socio_asociacion ga 
                          ON j.socio_asociacion_socio_idsocio = ga.socio_idsocio 
                          AND j.socio_asociacion_grupo_idgrupo = ga.grupo_idgrupo
                        INNER JOIN grupo g ON ga.grupo_idgrupo = g.idgrupo
                        INNER JOIN socio s ON ga.socio_idsocio = s.idsocio
                        INNER JOIN cargo c ON j.cargo_idcargo = c.idcargo
                        $where";

$totalFilteredResult = $conn->query($totalFilteredQuery);
$totalFiltered = $totalFilteredResult->fetch_assoc()['total'];

$data = [];
$counter = $offset + 1;
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "num" => $counter++,
        "dni_socio" => $row['dni_socio'],
        "nombre_socio" => $row['nombre_socio'] . ' ' . $row['apellido_pat'] . ' ' . $row['apellido_mat'],
        "nombre_grupo" => $row['nombre_grupo'],
        "nombre_cargo" => $row['nombre_cargo'],
        "fecha_inicio" => $row['fecha_inicio'],
        "fecha_fin" => $row['fecha_fin'],
        "celular" => $row['celular'],
        "acciones" => '
    <div class="d-flex justify-content-center">
        <button class="btn btn-warning btn-sm mr-2" onclick="editarJunta(' . $row['idjunta_directiva'] . ')">
            <i class="fas fa-edit"></i>
        </button>
        <button class="btn btn-danger btn-sm" onclick="confirmarEliminar(' . $row['idjunta_directiva'] . ')">
            <i class="fas fa-trash-alt"></i>
        </button>
    </div>'
    ];
}

// Respuesta JSON para DataTables
$response = [
    "draw" => intval($_POST['draw'] ?? 1),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
];

echo json_encode($response);
exit;
