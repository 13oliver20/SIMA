<?php
// Verificar si los archivos existen antes de requerirlos
require_once(__DIR__ . "/../../includes/conexion.php");

// Verificar conexión
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}

// Parámetros enviados por DataTables
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';

// Consulta total de registros sin filtrar
$sql_total = "SELECT COUNT(*) as total FROM resolucion_gdh";
$result_total = $conn->query($sql_total);
$totalData = $result_total->fetch_assoc()['total'];

// Consulta con búsqueda
$sql = "SELECT rg.idresolucion_gdh, rg.num_resolucion, rg.fecha_emision, rg.archivo_gdh, 
               g.nombre_grupo, g.etiqueta_grupo 
        FROM resolucion_gdh rg
        JOIN grupo g ON rg.grupo_idgrupo = g.idgrupo 
        WHERE 1=1";

if (!empty($search)) {
    $sql .= " AND (rg.num_resolucion LIKE '%$search%' 
                OR g.nombre_grupo LIKE '%$search%' 
                OR g.etiqueta_grupo LIKE '%$search%' 
                OR DATE_FORMAT(rg.fecha_emision, '%d/%m/%Y') LIKE '%$search%'
                OR rg.archivo_gdh LIKE '%$search%')";
}

$sql .= " LIMIT $start, $length";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $row['fecha_emision'] = date("d/m/Y", strtotime($row['fecha_emision']));
    $data[] = $row;
}

// Enviar respuesta en formato JSON
$response = [
    "draw" => $draw,
    "recordsTotal" => $totalData,
    "recordsFiltered" => $totalData,
    "data" => $data
];

echo json_encode($response);
$conn->close();
?>
