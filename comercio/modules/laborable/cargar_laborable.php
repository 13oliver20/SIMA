<?php
session_start();
require_once(__DIR__ . "/../../includes/conexion.php");

header('Content-Type: application/json');

if (!$conn) {
    die(json_encode(["error" => "No se pudo conectar a la base de datos."]));
}

// Columnas disponibles para ordenamiento (coinciden con las columnas que recibe DataTables en el frontend)
$columns = [
    0 => 'g.idgrupo',
    1 => 'g.nombre_grupo',
    2 => 'g.etiqueta_grupo',
    3 => 'a.nom_agrupamiento',
    4 => 'Lunes',
    5 => 'Martes',
    6 => 'Miércoles',
    7 => 'Jueves',
    8 => 'Viernes',
    9 => 'Sábado',
    10 => 'Domingo'
];

// Obtener parámetros
$searchValue = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';
$limit = isset($_POST['length']) ? (int) $_POST['length'] : 10;
$offset = isset($_POST['start']) ? (int) $_POST['start'] : 0;
$orderColumnIndex = $_POST['order'][0]['column'] ?? 1;
$orderDir = $_POST['order'][0]['dir'] === 'desc' ? 'DESC' : 'ASC';
$orderColumn = $columns[$orderColumnIndex] ?? 'g.nombre_grupo';

// Filtro de búsqueda
$where = " WHERE dl.dia LIKE '%$searchValue%' OR g.nombre_grupo LIKE '%$searchValue%' OR a.nom_agrupamiento LIKE '%$searchValue%' ";

// Contar registros filtrados
$sql_count = "
SELECT COUNT(DISTINCT g.idgrupo) as total 
FROM dia_laborable_has_grupo dlhg
JOIN grupo g ON dlhg.grupo_idgrupo = g.idgrupo
JOIN dia_laborable dl ON dl.iddia_laborable = dlhg.dia_laborable_iddia_laborable
JOIN agrupamiento a ON g.agrupamiento_idagrupamiento = a.idagrupamiento
$where
";

$totalRecordsResult = $conn->query($sql_count);
$totalRecords = $totalRecordsResult ? ($totalRecordsResult->fetch_assoc()['total'] ?? 0) : 0;

// Consulta principal con ordenamiento y paginación
$sql = "
SELECT 
    g.idgrupo,
    g.nombre_grupo,
    g.etiqueta_grupo, 
    a.nom_agrupamiento AS agrupamiento,
    MAX(CASE WHEN dl.dia = 'Lunes' THEN 'Lunes' ELSE '' END) AS Lunes,
    MAX(CASE WHEN dl.dia = 'Martes' THEN 'Martes' ELSE '' END) AS Martes,
    MAX(CASE WHEN dl.dia = 'Miércoles' THEN 'Miércoles' ELSE '' END) AS Miércoles,
    MAX(CASE WHEN dl.dia = 'Jueves' THEN 'Jueves' ELSE '' END) AS Jueves,
    MAX(CASE WHEN dl.dia = 'Viernes' THEN 'Viernes' ELSE '' END) AS Viernes,
    MAX(CASE WHEN dl.dia = 'Sábado' THEN 'Sábado' ELSE '' END) AS Sábado,
    MAX(CASE WHEN dl.dia = 'Domingo' THEN 'Domingo' ELSE '' END) AS Domingo
FROM 
    dia_laborable_has_grupo dlhg
JOIN 
    grupo g ON dlhg.grupo_idgrupo = g.idgrupo
JOIN 
    dia_laborable dl ON dl.iddia_laborable = dlhg.dia_laborable_iddia_laborable
JOIN 
    agrupamiento a ON g.agrupamiento_idagrupamiento = a.idagrupamiento
$where
GROUP BY 
    g.idgrupo
ORDER BY $orderColumn $orderDir
LIMIT $limit OFFSET $offset
";

$result = $conn->query($sql);

if (!$result) {
    die(json_encode(["error" => "Error en la consulta: " . $conn->error]));
}

// Armar array de datos
$data = [];
$counter = $offset + 1;

while ($row = $result->fetch_assoc()) {
    $id = $row['idgrupo'];
    $data[] = [
        "num" => $counter++,
        "idgrupo" => $row['idgrupo'],
        "nombre_grupo" => $row['nombre_grupo'],
        "etiqueta_grupo" => $row['etiqueta_grupo'],
        "agrupamiento" => $row['agrupamiento'],
        "Lunes" => $row['Lunes'],
        "Martes" => $row['Martes'],
        "Miércoles" => $row['Miércoles'],
        "Jueves" => $row['Jueves'],
        "Viernes" => $row['Viernes'],
        "Sábado" => $row['Sábado'],
        "Domingo" => $row['Domingo'],
        "acciones" => '<div class="d-flex justify-content-center">
            <button class="btn btn-warning btn-sm mr-2" data-id="' . $id . '">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-danger btn-sm" data-id="' . $id . '">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>'
    ];
}

// Respuesta para DataTables
echo json_encode([
    "draw" => intval($_POST['draw'] ?? 1),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $data
]);

$conn->close();
?>
