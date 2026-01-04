<?php
require_once(__DIR__ . "/../../includes/conexion.php");

$searchValue = $_POST['search']['value'] ?? '';
$limit = $_POST['length'] ?? 10;
$offset = $_POST['start'] ?? 0;
$grupo_id = $_POST['grupo_id'] ?? ''; // Filtro de grupo

// Base de la consulta
$where = " WHERE (SELECT COUNT(DISTINCT sa1.grupo_idgrupo) FROM socio_asociacion sa1 WHERE sa1.socio_idsocio = s.idsocio) > 1 ";
$params = [];

if (!empty($grupo_id)) {
    $where .= " AND (sa.grupo_idgrupo = ? OR sa.socio_idsocio IN (SELECT socio_idsocio FROM socio_asociacion WHERE grupo_idgrupo = ?)) ";
    array_push($params, $grupo_id, $grupo_id);
}

if (!empty($searchValue)) {
    $where .= " AND (s.dni LIKE ? OR s.nombre LIKE ? OR s.apellido_pat LIKE ? OR g.nombre_grupo LIKE ?) ";
    $searchValue = "%$searchValue%";
    array_push($params, $searchValue, $searchValue, $searchValue, $searchValue);
}

// Consulta principal
$query = "
SELECT SQL_CALC_FOUND_ROWS
    s.idsocio,
    s.dni,
    CONCAT(s.nombre, ' ', s.apellido_pat, ' ', IFNULL(s.apellido_mat, '')) AS nombre_completo,
    (SELECT COUNT(DISTINCT sa1.grupo_idgrupo) FROM socio_asociacion sa1 WHERE sa1.socio_idsocio = s.idsocio) AS total_asociaciones,
    g.nombre_grupo AS asociaciones
FROM socio s
JOIN socio_asociacion sa ON s.idsocio = sa.socio_idsocio
JOIN grupo g ON sa.grupo_idgrupo = g.idgrupo
$where
ORDER BY s.idsocio, g.nombre_grupo
LIMIT ? OFFSET ?
";

array_push($params, $limit, $offset);
$stmt = mysqli_prepare($conn, $query);

// Generar los tipos dinámicamente
$types = str_repeat("s", count($params) - 2) . "ii";
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$data = [];
$socios_previos = [];
$line_counter = $offset + 1;

while ($row = mysqli_fetch_assoc($result)) {
    if (!isset($socios_previos[$row['dni']])) {
        $socios_previos[$row['dni']] = true;
        $data[] = [
            "num" => $line_counter++,
            "dni" => $row['dni'],
            "nombre_completo" => $row['nombre_completo'],
            "total_asociaciones" => $row['total_asociaciones'],
            "asociaciones" => $row['asociaciones']
        ];
    } else {
        $data[] = [
            "num" => $line_counter++,
            "dni" => "",
            "nombre_completo" => "",
            "total_asociaciones" => "",
            "asociaciones" => $row['asociaciones']
        ];
    }
}

// Obtener total de registros filtrados
$totalFilteredResult = mysqli_query($conn, "SELECT FOUND_ROWS() AS total");
$totalFiltered = mysqli_fetch_assoc($totalFilteredResult)['total'] ?? 0;

// Obtener total de registros sin filtro
$totalRecordsQuery = "SELECT COUNT(DISTINCT s.idsocio) AS total FROM socio s";
$totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
$totalRecords = mysqli_fetch_assoc($totalRecordsResult)['total'] ?? 0;

// Responder en formato JSON
$response = [
    "draw" => intval($_POST['draw'] ?? 1),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data" => $data
];

echo json_encode($response);
?>
