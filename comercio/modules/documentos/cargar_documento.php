<?php
require_once(__DIR__ . "/../../includes/conexion.php");

header('Content-Type: application/json'); // Asegurar respuesta JSON

$columns = ['idgrupo', 'etiqueta_grupo', 'nombre_grupo', 'partida_registral', 'fecha_fundacion', 'archivo_acta', 'archivo_vigencia', 'archivo_padron', 'archivo_gdh'];

$searchValue = isset($_POST['search']['value']) ? mysqli_real_escape_string($conn, $_POST['search']['value']) : '';
$limit = intval($_POST['length'] ?? 10);
$offset = intval($_POST['start'] ?? 0);

// Ordenamiento
$orderBy = "";
if (isset($_POST['order'][0])) {
    $orderColumnIndex = intval($_POST['order'][0]['column']);
    $orderDir = $_POST['order'][0]['dir'] === 'desc' ? 'DESC' : 'ASC';

    // Validar que la columna exista en el array $columns
    if (isset($columns[$orderColumnIndex])) {
        $orderBy = " ORDER BY " . $columns[$orderColumnIndex] . " " . $orderDir;
    }
}

// Condición de búsqueda
$where = " WHERE 1=1 ";
if (!empty($searchValue)) {
    $where .= " AND (
        g.nombre_grupo LIKE '%$searchValue%' OR 
        g.etiqueta_grupo LIKE '%$searchValue%' OR 
        v.partida_registral LIKE '%$searchValue%' OR 
        a.fecha_fundacion LIKE '%$searchValue%'
    )";
}

// Query principal con paginación y ordenamiento
$query = "
SELECT 
    g.idgrupo, 
    g.etiqueta_grupo, 
    g.nombre_grupo, 
    v.partida_registral, 
    a.fecha_fundacion, 
    a.archivo_acta, 
    v.archivo_vigencia, 
    p.archivo_padron, 
    r.archivo_gdh
FROM grupo g
LEFT JOIN vigencia_poder v ON g.idgrupo = v.grupo_idgrupo
LEFT JOIN acta_constitucion a ON g.idgrupo = a.grupo_idgrupo
LEFT JOIN padron_socios p ON g.idgrupo = p.grupo_idgrupo
LEFT JOIN resolucion_gdh r ON g.idgrupo = r.grupo_idgrupo
$where
$orderBy
LIMIT $limit OFFSET $offset
";

$result = mysqli_query($conn, $query);
$data = [];
$counter = $offset + 1;

// Función para generar botón con icono PDF
function generarEnlaceArchivo($archivo, $tipo) {
    if (!empty($archivo)) {
        $rutaBase = "/COMERCIO/uploads/";
        $carpetas = [
            "archivo_acta" => "constitucion/",
            "archivo_vigencia" => "vigencia/",
            "archivo_padron" => "padron/",
            "archivo_gdh" => "resolucion/"
        ];
        
        $rutaArchivo = $rutaBase . $carpetas[$tipo] . $archivo;
        return "<a href='$rutaArchivo' target='_blank' class='d-flex justify-content-center'>
                   <i class='fas fa-file-pdf text-danger' style='font-size: 24px;'></i>
               </a>";
    } else {
        return '-';
    }
}

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "num" => $counter++,
        "etiqueta_grupo" => $row['etiqueta_grupo'],
        "nombre_grupo" => $row['nombre_grupo'],
        "partida_registral" => $row['partida_registral'] ?? '-',
        "fecha_fundacion" => $row['fecha_fundacion'] ?? '-',
        "archivo_acta" => generarEnlaceArchivo($row['archivo_acta'], "archivo_acta"),
        "archivo_vigencia" => generarEnlaceArchivo($row['archivo_vigencia'], "archivo_vigencia"),
        "archivo_padron" => generarEnlaceArchivo($row['archivo_padron'], "archivo_padron"),
        "archivo_gdh" => generarEnlaceArchivo($row['archivo_gdh'], "archivo_gdh")
    ];
}

// Total de registros sin filtro
$totalRecordsQuery = "SELECT COUNT(*) AS total FROM grupo";
$totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
$totalRecords = mysqli_fetch_assoc($totalRecordsResult)['total'] ?? 0;

// Total de registros filtrados
$totalFilteredQuery = "
SELECT COUNT(*) AS total FROM grupo g
LEFT JOIN vigencia_poder v ON g.idgrupo = v.grupo_idgrupo
LEFT JOIN acta_constitucion a ON g.idgrupo = a.grupo_idgrupo
LEFT JOIN padron_socios p ON g.idgrupo = p.grupo_idgrupo
LEFT JOIN resolucion_gdh r ON g.idgrupo = r.grupo_idgrupo
$where
";

$totalFilteredResult = mysqli_query($conn, $totalFilteredQuery);
$totalFiltered = mysqli_fetch_assoc($totalFilteredResult)['total'] ?? 0;

// Respuesta JSON
$response = [
    "draw" => intval($_POST['draw'] ?? 1),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data" => $data
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
