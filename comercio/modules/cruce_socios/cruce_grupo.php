<?php
require_once(__DIR__ . "/../../includes/conexion.php");

$searchValueRaw = $_POST['search']['value'] ?? '';
$limit = intval($_POST['length'] ?? 10);
$offset = intval($_POST['start'] ?? 0);

// Construcción condición búsqueda
$where = " WHERE 1=1 ";
$params = [];
$types = "";

// Filtro de búsqueda (nombre grupo, etiqueta o agrupamiento)
if ($searchValueRaw !== '') {
    $searchValue = "%$searchValueRaw%";
    $where .= " AND (g.nombre_grupo LIKE ? OR g.etiqueta_grupo LIKE ? OR a.nom_agrupamiento LIKE ?) ";
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $types .= "sss";
}

// Concatenamos LIMIT y OFFSET directamente (más seguro al forzar int)
$limit = max($limit, 1);
$offset = max($offset, 0);

// Consulta para grupos con socios en más de una asociación
$query = "
SELECT 
    g.idgrupo,
    g.nombre_grupo,
    g.etiqueta_grupo,
    a.nom_agrupamiento,
    COUNT(DISTINCT sa.socio_idsocio) AS cantidad_socios_cruzados
FROM grupo g
JOIN agrupamiento a ON g.agrupamiento_idagrupamiento = a.idagrupamiento
JOIN socio_asociacion sa ON g.idgrupo = sa.grupo_idgrupo
JOIN (
    SELECT socio_idsocio
    FROM socio_asociacion
    GROUP BY socio_idsocio
    HAVING COUNT(DISTINCT grupo_idgrupo) > 1
) sc ON sc.socio_idsocio = sa.socio_idsocio
$where
GROUP BY g.idgrupo, g.nombre_grupo, g.etiqueta_grupo, a.nom_agrupamiento
LIMIT $limit OFFSET $offset
";

$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    die("Error en prepare: " . mysqli_error($conn));
}

if (!empty($params)) {
    // Preparar bind_param dinámicamente
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$data = [];
$counter = $offset + 1;

while ($row = mysqli_fetch_assoc($result)) {
    $idgrupo = $row['idgrupo'];
    $data[] = [
        "num" => $counter++,
        "nombre_grupo" => $row['nombre_grupo'],
        "etiqueta_grupo" => $row['etiqueta_grupo'],
        "nom_agrupamiento" => $row['nom_agrupamiento'],
        "cantidad_socios_cruzados" => $row['cantidad_socios_cruzados'],
        "padron" => '
            <div style="white-space: nowrap;">
                <a href="modules/cruce_socios/reporte_crucegrupo.php?idgrupo=' . $idgrupo . '" target="_blank" class="btn btn-sm btn-danger mx-1" title="Exportar PDF">
                    <i class="fas fa-file-pdf"></i>
                </a>
                <a href="modules/cruce_socios/reporte_excelcruce.php?idgrupo=' . $idgrupo . '" class="btn btn-sm btn-success mx-1" title="Exportar Excel">
                    <i class="fas fa-file-excel"></i>
                </a>
            </div>'
    ];
}

// Conteo total sin filtro
$totalRecordsQuery = "
SELECT COUNT(DISTINCT g.idgrupo)
FROM grupo g
JOIN socio_asociacion sa ON g.idgrupo = sa.grupo_idgrupo
JOIN (
    SELECT socio_idsocio
    FROM socio_asociacion
    GROUP BY socio_idsocio
    HAVING COUNT(DISTINCT grupo_idgrupo) > 1
) sc ON sc.socio_idsocio = sa.socio_idsocio
";

$totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
$totalRecords = mysqli_fetch_row($totalRecordsResult)[0] ?? 0;

// Conteo total con filtro
$filteredRecordsQuery = "
SELECT COUNT(DISTINCT g.idgrupo)
FROM grupo g
JOIN agrupamiento a ON g.agrupamiento_idagrupamiento = a.idagrupamiento
JOIN socio_asociacion sa ON g.idgrupo = sa.grupo_idgrupo
JOIN (
    SELECT socio_idsocio
    FROM socio_asociacion
    GROUP BY socio_idsocio
    HAVING COUNT(DISTINCT grupo_idgrupo) > 1
) sc ON sc.socio_idsocio = sa.socio_idsocio
$where
";

$stmt2 = mysqli_prepare($conn, $filteredRecordsQuery);
if (!$stmt2) {
    die("Error prepare filtered: " . mysqli_error($conn));
}

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt2, $types, ...$params);
}

mysqli_stmt_execute($stmt2);
$result2 = mysqli_stmt_get_result($stmt2);
$filteredRecords = mysqli_fetch_row($result2)[0] ?? 0;

// Respuesta JSON
$response = [
    "draw" => intval($_POST['draw'] ?? 1),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($filteredRecords),
    "data" => $data
];

header('Content-Type: application/json');
echo json_encode($response);
