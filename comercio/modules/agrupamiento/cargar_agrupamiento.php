<?php
require_once(__DIR__ . "/../../includes/conexion.php");

// Verificar conexión
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}

// Parámetros de búsqueda y paginación
$searchValue = isset($_POST['search']['value']) ? mysqli_real_escape_string($conn, $_POST['search']['value']) : '';
$limit = $_POST['length'] ?? 10;
$offset = $_POST['start'] ?? 0;

// Condición de búsqueda
$where = " WHERE 1=1 ";
if (!empty($searchValue)) {
    $where .= " AND (cod_etiqueta LIKE '%$searchValue%' OR nom_agrupamiento LIKE '%$searchValue%') ";
}

// Consulta para obtener los datos paginados, incluyendo el conteo de grupos
$query = "
    SELECT 
        idagrupamiento, 
        cod_etiqueta, 
        nom_agrupamiento, 
        (SELECT COUNT(*) FROM grupo WHERE agrupamiento_idagrupamiento = agrupamiento.idagrupamiento) AS cantidad_grupos
    FROM 
        agrupamiento 
    $where 
    LIMIT $limit OFFSET $offset
";
$result = mysqli_query($conn, $query);

$data = [];
$counter = $offset + 1;
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "num" => $counter++,
        "cod_etiqueta" => $row['cod_etiqueta'],
        "nom_agrupamiento" => $row['nom_agrupamiento'],
        "cantidad_grupos" => $row['cantidad_grupos'], // Mostramos la cantidad de grupos
        "acciones" => $row['acciones'] = '
    <div class="d-flex justify-content-center">
        <button class="btn btn-warning btn-sm mr-2" onclick="editarAgrupamiento(' . $row['idagrupamiento'] . ')">
            <i class="fas fa-edit"></i>
        </button>
        <button class="btn btn-danger btn-sm btnEliminar" data-id="' . $row['idagrupamiento'] . '">
            <i class="fas fa-trash-alt"></i>
        </button>
    </div>'
    ];
}

// Total de registros sin filtro
$totalRecordsQuery = "SELECT COUNT(*) AS total FROM agrupamiento";
$totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
$totalRecords = mysqli_fetch_assoc($totalRecordsResult)['total'];

// Total de registros filtrados
$totalFilteredQuery = "SELECT COUNT(*) AS total FROM agrupamiento $where";
$totalFilteredResult = mysqli_query($conn, $totalFilteredQuery);
$totalFiltered = mysqli_fetch_assoc($totalFilteredResult)['total'];

// Respuesta en formato JSON para DataTables
$response = [
    "draw" => intval($_POST['draw'] ?? 1),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data" => $data
];

echo json_encode($response);
?>
