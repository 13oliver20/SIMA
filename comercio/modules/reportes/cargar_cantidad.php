<?php
require_once(__DIR__ . "/../../includes/conexion.php");

$columns = ['nombre_grupo', 'etiqueta_grupo', 'nom_agrupamiento', 'cantidad_socios'];
$searchValue = isset($_POST['search']['value']) ? mysqli_real_escape_string($conn, $_POST['search']['value']) : '';
$limit = $_POST['length'] ?? 10;
$offset = $_POST['start'] ?? 0;

// Construcción de condición de búsqueda
$where = " WHERE 1=1 ";
if (!empty($searchValue)) {
    $where .= " AND (g.nombre_grupo LIKE '%$searchValue%' 
                  OR g.etiqueta_grupo LIKE '%$searchValue%' 
                  OR a.nom_agrupamiento LIKE '%$searchValue%') ";
}

// Consulta principal con paginación
$query = "
SELECT 
    g.idgrupo, 
    g.nombre_grupo, 
    g.etiqueta_grupo, 
    a.nom_agrupamiento, 
    COUNT(sa.socio_idsocio) AS cantidad_socios,
    'Padron Ejemplo' AS padron
FROM grupo g
LEFT JOIN socio_asociacion sa ON g.idgrupo = sa.grupo_idgrupo
LEFT JOIN agrupamiento a ON g.agrupamiento_idagrupamiento = a.idagrupamiento
$where
GROUP BY g.idgrupo, g.nombre_grupo, g.etiqueta_grupo, a.nom_agrupamiento
ORDER BY g.idgrupo
LIMIT $limit OFFSET $offset
";

$result = mysqli_query($conn, $query);
$data = [];
$counter = $offset + 1;

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "num" => $counter++,
        "nombre_grupo" => $row['nombre_grupo'],
        "etiqueta_grupo" => $row['etiqueta_grupo'],
        "nom_agrupamiento" => $row['nom_agrupamiento'],
        "cantidad_socios" => $row['cantidad_socios'],
        "padron" => '
            <div style="white-space: nowrap;">
                <a href="modules/reportes/generar_padronv.php?idgrupo=' . $row['idgrupo'] . '" target="_blank" class="btn btn-sm btn-danger mx-1" title="Exportar PDF">
                    <i class="fas fa-file-pdf"></i>
                </a>
                <a href="modules/reportes/generar_excelv.php?idgrupo=' . $row['idgrupo'] . '" class="btn btn-sm btn-success mx-1" title="Exportar Excel">
                    <i class="fas fa-file-excel"></i>
                </a>
            </div>'
    ];
}

// Total sin filtro
$totalRecordsQuery = "SELECT COUNT(*) AS total FROM grupo";
$totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
$totalRecords = mysqli_fetch_assoc($totalRecordsResult)['total'] ?? 0;

// Total con filtro
$filteredRecordsQuery = "
SELECT COUNT(*) AS filtered
FROM grupo g
LEFT JOIN agrupamiento a ON g.agrupamiento_idagrupamiento = a.idagrupamiento
$where
";
$filteredResult = mysqli_query($conn, $filteredRecordsQuery);
$filteredRecords = mysqli_fetch_assoc($filteredResult)['filtered'] ?? 0;

// Respuesta
$response = [
    "draw" => intval($_POST['draw'] ?? 1),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
];

echo json_encode($response);
?>
