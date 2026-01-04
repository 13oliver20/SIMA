<?php
require_once(__DIR__ . "/../../includes/conexion.php");

// Parámetros de DataTables
$start  = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;
$search = $_POST['search']['value'] ?? '';

// Filtro de búsqueda
$searchQuery = "";
if (!empty($search)) {
    $searchQuery = " AND (s.nombre LIKE '%$search%' OR s.apellido_pat LIKE '%$search%' OR s.apellido_mat LIKE '%$search%')";
}

// Obtener total de registros
$count_query = "
SELECT COUNT(DISTINCT s.idsocio, g.idgrupo) AS total
FROM verificacion_asociados va
LEFT JOIN socio s ON va.socio_asociacion_socio_idsocio = s.idsocio
LEFT JOIN grupo g ON va.socio_asociacion_grupo_idgrupo = g.idgrupo
WHERE 1=1 $searchQuery
";
$count_result = mysqli_query($conn, $count_query);
$total_registros = mysqli_fetch_assoc($count_result)['total'];

// Obtener datos paginados
$query = "
SELECT 
    s.idsocio, s.dni,
    IFNULL(s.nombre, 'N/A') AS nombre,
    IFNULL(s.apellido_pat, 'N/A') AS apellido_pat,
    IFNULL(s.apellido_mat, 'N/A') AS apellido_mat,
    IFNULL(g.nombre_grupo, 'N/A') AS grupo,
    MAX(CASE WHEN va.orden = 1 THEN va.verificacion END) AS primera_verificacion,
    MAX(CASE WHEN va.orden = 1 THEN va.fecha_verificacion END) AS primera_fecha,
    MAX(CASE WHEN va.orden = 2 THEN va.verificacion END) AS segunda_verificacion,
    MAX(CASE WHEN va.orden = 2 THEN va.fecha_verificacion END) AS segunda_fecha,
    MAX(CASE WHEN va.orden = 3 THEN va.verificacion END) AS tercera_verificacion,
    MAX(CASE WHEN va.orden = 3 THEN va.fecha_verificacion END) AS tercera_fecha,
    CASE  
        WHEN 
            SUM(CASE WHEN va.verificacion = 'Presente' THEN 1 ELSE 0 END) > 0 OR 
            SUM(CASE WHEN va.verificacion = 'Justificado' THEN 1 ELSE 0 END) > 0 
        THEN 'Activo'  
        WHEN 
            SUM(CASE WHEN va.verificacion IS NOT NULL THEN 1 ELSE 0 END) = 0 
        THEN 'En espera'
        ELSE 'Inactivo'  
    END AS estado  
FROM (
    SELECT 
        va.*, 
        ROW_NUMBER() OVER (
            PARTITION BY va.socio_asociacion_socio_idsocio, va.socio_asociacion_grupo_idgrupo ORDER BY va.fecha_verificacion
        ) AS orden
    FROM verificacion_asociados va
) va
LEFT JOIN socio s ON va.socio_asociacion_socio_idsocio = s.idsocio
LEFT JOIN grupo g ON va.socio_asociacion_grupo_idgrupo = g.idgrupo
WHERE 1=1 $searchQuery
GROUP BY s.idsocio, g.idgrupo, s.nombre, s.apellido_pat, s.apellido_mat, g.nombre_grupo
ORDER BY s.idsocio, g.nombre_grupo
LIMIT $length OFFSET $start
";

$result = mysqli_query($conn, $query);

// Formato de salida JSON
$data = [];
$contador = $start + 1; // Inicia el contador desde la página actual

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "contador" => $contador, // Agregar el contador en la columna de la primera columna
        "dni" => $row['dni'],
        "nombre" => $row['nombre'],
        "apellidos" => $row['apellido_pat'] . " " . $row['apellido_mat'],
        "grupo" => $row['grupo'],
        "primera_verificacion" => $row['primera_verificacion'],
        "primera_fecha" => $row['primera_fecha'],
        "segunda_verificacion" => $row['segunda_verificacion'],
        "segunda_fecha" => $row['segunda_fecha'],
        "tercera_verificacion" => $row['tercera_verificacion'],
        "tercera_fecha" => $row['tercera_fecha'],
        "estado" => $row['estado']
    ];
    $contador++; // Incrementa el contador para la siguiente fila
}

// Respuesta para DataTables
$response = [
    "draw" => intval($_POST['draw'] ?? 0),
    "recordsTotal" => $total_registros,
    "recordsFiltered" => $total_registros,
    "data" => $data
];

echo json_encode($response);
mysqli_close($conn);
?>
