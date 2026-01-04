<?php
require_once(__DIR__ . "/../../includes/conexion.php");

if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}

// Parámetros enviados por DataTables
$start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

// Consulta principal con búsqueda
$query = "SELECT ps.idpadron_socios, ps.archivo_padron, g.etiqueta_grupo, g.nombre_grupo
          FROM padron_socios ps
          JOIN grupo g ON ps.grupo_idgrupo = g.idgrupo
          WHERE ps.archivo_padron LIKE ? OR g.nombre_grupo LIKE ? OR g.etiqueta_grupo LIKE ?
          LIMIT ?, ?";
$stmt = $conn->prepare($query);
$search_param = "%$search%";
$stmt->bind_param("ssssi", $search_param, $search_param, $search_param, $start, $length);
$stmt->execute();
$result = $stmt->get_result();

// Contar total de registros
$query_total = "SELECT COUNT(*) as total FROM padron_socios ps 
                JOIN grupo g ON ps.grupo_idgrupo = g.idgrupo 
                WHERE ps.archivo_padron LIKE ? OR g.nombre_grupo LIKE ? OR g.etiqueta_grupo LIKE ?";
$stmt_total = $conn->prepare($query_total);
$stmt_total->bind_param("sss", $search_param, $search_param, $search_param);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_padron = $result_total->fetch_assoc()['total'];

// Construcción de datos para DataTables
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'idpadron_socios' => $row['idpadron_socios'],
        'etiqueta_grupo' => $row['etiqueta_grupo'],
        'nombre_grupo' => $row['nombre_grupo'],
        'archivo_padron' => $row['archivo_padron'],
        'acciones' => $row['idpadron_socios'] // Se usará para los botones
    ];
}

// Enviar datos en formato JSON
$response = [
    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    "recordsTotal" => $total_padron,
    "recordsFiltered" => $total_padron,
    "data" => $data
];

echo json_encode($response);

// Cerrar conexión
$stmt->close();
$stmt_total->close();
$conn->close();
?>
