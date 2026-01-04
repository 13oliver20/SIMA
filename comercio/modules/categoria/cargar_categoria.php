<?php
session_start();
include '../../includes/conexion.php';

// Parámetros de búsqueda y paginación
$searchValue = isset($_POST['search']['value']) ? mysqli_real_escape_string($conn, $_POST['search']['value']) : '';
$limit = $_POST['length'] ?? 5;
$offset = $_POST['start'] ?? 0;

// Condición de búsqueda
$where = " WHERE 1=1 ";
if (!empty($searchValue)) {
    $where .= " AND tipo LIKE '%$searchValue%' ";
}

// Consulta para obtener los datos paginados
$query = "SELECT idcategoria, tipo FROM categoria $where LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

$data = [];
$counter = $offset + 1;
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "num" => $counter++,
        "tipo" => $row['tipo'],
        "acciones" => '
    <div class="d-flex justify-content-center">
        <button class="btn btn-warning btn-sm mr-2" onclick="editarCategoria(' . $row['idcategoria'] . ')">
            <i class="fas fa-edit"></i>
        </button>
        <button class="btn btn-danger btn-sm btnEliminarCategoria" onclick="confirmarEliminar(' . $row['idcategoria'] . ')">
            <i class="fas fa-trash-alt"></i>
        </button>
    </div>'
    ];
}

// Total de registros sin filtro
$totalRecordsQuery = "SELECT COUNT(*) AS total FROM categoria";
$totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
$totalRecords = mysqli_fetch_assoc($totalRecordsResult)['total'];

// Total de registros filtrados
$totalFilteredQuery = "SELECT COUNT(*) AS total FROM categoria $where";
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
