<?php
require_once(__DIR__ . "/../../includes/conexion.php");

// Función para generar el enlace con el icono PDF
function generarEnlaceArchivo($archivo) {
    if (!empty($archivo)) {
        $rutaBase = "/comercio/uploads/verificacion/"; // Ajusta la ruta
        return "<a href='$rutaBase$archivo' target='_blank' class='d-flex justify-content-center'>
                   <i class='fas fa-file-pdf text-danger' style='font-size: 24px;'></i>
               </a>";
    } else {
        return '-';
    }
}

// Configuración de paginación y búsqueda
$registros_por_pagina = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$pagina_actual = isset($_POST['start']) ? (int)($_POST['start'] / $registros_por_pagina) + 1 : 1;
$busqueda = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
$busqueda = mysqli_real_escape_string($conn, $busqueda); 

$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Obtener total de registros
$count_query = "
SELECT COUNT(DISTINCT g.idgrupo) AS total
FROM data_asociaciones.acta_verificacion av
INNER JOIN data_asociaciones.acta_verificacion_has_grupo avhg
ON av.idacta_verificacion = avhg.acta_verificacion_idacta_verificacion
INNER JOIN data_asociaciones.grupo g
ON g.idgrupo = avhg.grupo_idgrupo
WHERE g.nombre_grupo LIKE '%$busqueda%'
";
$count_result = mysqli_query($conn, $count_query);
$total_registros = mysqli_fetch_assoc($count_result)['total'];

// Obtener datos con paginación
$query = "
SELECT 
    g.idgrupo,
    g.nombre_grupo,
    IFNULL(GROUP_CONCAT(av.num_verificacion ORDER BY avhg.fecha_verificacion ASC SEPARATOR '|||'), '') AS verificacion,
    IFNULL(GROUP_CONCAT(avhg.fecha_verificacion ORDER BY avhg.fecha_verificacion ASC SEPARATOR '|||'), '') AS fechas,
    IFNULL(GROUP_CONCAT(avhg.archivo_verificacion ORDER BY avhg.fecha_verificacion ASC SEPARATOR '|||'), '') AS archivos
FROM data_asociaciones.acta_verificacion av
INNER JOIN data_asociaciones.acta_verificacion_has_grupo avhg
ON av.idacta_verificacion = avhg.acta_verificacion_idacta_verificacion
INNER JOIN data_asociaciones.grupo g
ON g.idgrupo = avhg.grupo_idgrupo
WHERE g.nombre_grupo LIKE '%$busqueda%'
GROUP BY g.idgrupo
LIMIT $registros_por_pagina OFFSET $offset
";

$result = mysqli_query($conn, $query);

$contador = $offset + 1; // Contador para numeración de filas
$datos = [];

while ($row = mysqli_fetch_assoc($result)) {
    $verificaciones = explode("|||", $row['verificacion'] ?? '');
    $fechas = explode("|||", $row['fechas'] ?? '');
    $archivos = explode("|||", $row['archivos'] ?? '');

    // Asegurar tres verificaciones
    for ($i = 0; $i < 3; $i++) {
        $verificaciones[$i] = $verificaciones[$i] ?? "—";
        $fechas[$i] = $fechas[$i] ?? "—";
        $archivos[$i] = $archivos[$i] ?? "";
    }

    // Generar enlaces a archivos
    $archivo_1 = generarEnlaceArchivo($archivos[0]);
    $archivo_2 = generarEnlaceArchivo($archivos[1]);
    $archivo_3 = generarEnlaceArchivo($archivos[2]);

    // Botones de acción
    $acciones = '
        <div class="text-center">
            <button class="btn btn-warning btn-sm edit-btn" data-id="' . $row['idgrupo'] . '">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-danger btn-sm delete-btn" data-id="' . $row['idgrupo'] . '">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    ';

    $datos[] = [
        'contador' => $contador++,
        'nombre_grupo' => $row['nombre_grupo'],
        'verificacion_1' => $verificaciones[0],
        'fecha_1' => $fechas[0],
        'archivo_1' => $archivo_1,
        'verificacion_2' => $verificaciones[1],
        'fecha_2' => $fechas[1],
        'archivo_2' => $archivo_2,
        'verificacion_3' => $verificaciones[2],
        'fecha_3' => $fechas[2],
        'archivo_3' => $archivo_3,
        'acciones' => $acciones
    ];
}

$response = [
    "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
    "recordsTotal" => $total_registros,
    "recordsFiltered" => $total_registros,
    "data" => $datos
];

header("Content-Type: application/json");
echo json_encode($response);

mysqli_close($conn);
?>
