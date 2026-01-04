<?php 
require_once(__DIR__ . "/../../includes/conexion.php");

// Parámetros enviados por DataTables
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? $conn->real_escape_string(trim($_POST['search']['value'])) : '';

// Consulta total de registros sin filtrar
$sql_total = "SELECT COUNT(*) as total FROM acta_constitucion";
$result_total = $conn->query($sql_total);
$totalData = $result_total->fetch_assoc()['total'];

// Consulta con búsqueda y conteo filtrado
$sql_filtered = "SELECT COUNT(*) as total FROM acta_constitucion ac
                 JOIN grupo g ON ac.grupo_idgrupo = g.idgrupo
                 WHERE 1=1";

if (!empty($search)) {
    $sql_filtered .= " AND (g.nombre_grupo LIKE '%$search%' 
                        OR g.etiqueta_grupo LIKE '%$search%' 
                        OR DATE_FORMAT(ac.fecha_fundacion, '%d/%m/%Y') LIKE '%$search%' 
                        OR ac.archivo_acta LIKE '%$search%')";
}

$result_filtered = $conn->query($sql_filtered);
$totalFiltered = $result_filtered->fetch_assoc()['total'];

// Consulta principal con límites y búsqueda
$sql = "SELECT ac.idacta_constitucion, ac.fecha_fundacion, ac.archivo_acta, 
               g.nombre_grupo, g.etiqueta_grupo 
        FROM acta_constitucion ac
        JOIN grupo g ON ac.grupo_idgrupo = g.idgrupo
        WHERE 1=1";

if (!empty($search)) {
    $sql .= " AND (g.nombre_grupo LIKE '%$search%' 
                OR g.etiqueta_grupo LIKE '%$search%' 
                OR DATE_FORMAT(ac.fecha_fundacion, '%d/%m/%Y') LIKE '%$search%' 
                OR ac.archivo_acta LIKE '%$search%')";
}

$sql .= " LIMIT $start, $length";

$result = $conn->query($sql);

$data = [];
$counter = $start + 1;
while ($row = $result->fetch_assoc()) {
    $fecha = date("d/m/Y", strtotime($row['fecha_fundacion']));

    $archivoHtml = $row['archivo_acta'] 
        ? '<a href="uploads/constitucion/' . $row['archivo_acta'] . '" target="_blank" title="Ver archivo">
             <i class="fas fa-file-pdf text-danger"></i>
           </a>'
        : '-';

    $acciones = '<div class="d-flex justify-content-center">
        <button class="btn btn-warning btn-sm mr-2 editar-acta" data-id="' . $row['idacta_constitucion'] . '" title="Editar">
            <i class="fas fa-edit"></i>
        </button>
        <button class="btn btn-danger btn-sm btnEliminar" data-id="' . $row['idacta_constitucion'] . '" title="Eliminar">
            <i class="fas fa-trash-alt"></i>
        </button>
    </div>';


    $data[] = [
        "num" => $counter++,
        "codigo" => $row['etiqueta_grupo'],
        "grupo" => $row['nombre_grupo'],
        "fecha_fundacion" => $fecha,
        "archivo" => $archivoHtml,
        "acciones" => $acciones
    ];
}

$response = [
    "draw" => $draw,
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
];

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
