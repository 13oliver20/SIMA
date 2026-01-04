<?php
require_once(__DIR__ . "/../../includes/conexion.php");

if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}

// Número de registros por página
$registros_por_pagina = 10;

// Parámetros enviados por DataTables
$start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

// Consulta para obtener los datos
$query = "SELECT v.idvigencia_poder, v.partida_registral, v.archivo_vigencia, g.nombre_grupo, g.etiqueta_grupo
          FROM vigencia_poder v
          JOIN grupo g ON v.grupo_idgrupo = g.idgrupo
          WHERE v.partida_registral LIKE ? OR g.nombre_grupo LIKE ? OR g.etiqueta_grupo LIKE ?
          LIMIT ?, ?";
$stmt = $conn->prepare($query);
$search_term = "%$search%";
$stmt->bind_param("ssssi", $search_term, $search_term, $search_term, $start, $length);
$stmt->execute();
$result = $stmt->get_result();

// Contar total de registros para la paginación
$query_total = "SELECT COUNT(*) as total FROM vigencia_poder v JOIN grupo g ON v.grupo_idgrupo = g.idgrupo 
                WHERE v.partida_registral LIKE ? OR g.nombre_grupo LIKE ? OR g.etiqueta_grupo LIKE ?";
$stmt_total = $conn->prepare($query_total);
$stmt_total->bind_param("sss", $search_term, $search_term, $search_term);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_vigencias = $result_total->fetch_assoc()['total'];

// Generar los datos para DataTables
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'idvigencia_poder' => $row['idvigencia_poder'],
        'partida_registral' => $row['partida_registral'],
        'archivo_vigencia' => $row['archivo_vigencia'],
        'nombre_grupo' => $row['nombre_grupo'],
        'etiqueta_grupo' => $row['etiqueta_grupo'],
        'acciones' => $row['idvigencia_poder'] // Se usará para los botones de acción
    ];
}

// Enviar respuesta en formato JSON
$response = [
    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    "recordsTotal" => $total_vigencias,
    "recordsFiltered" => $total_vigencias,
    "data" => $data
];

echo json_encode($response);

// Cerrar conexiones
$stmt->close();
$stmt_total->close();
$conn->close();
?>
