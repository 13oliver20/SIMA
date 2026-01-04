<?php
require_once(__DIR__ . "/../../includes/conexion.php");

$request = $_REQUEST;

// Columnas que puedes buscar (excluyendo 'acciones')
$columns = [
    'r.idrubro',
    'r.nombre',
    'sp.idsubrubro',
    'sp.nombre',
    'ss.idsubrubro_seg',
    'ss.nombre'
];

// Consulta base
$sqlBase = "FROM rubro_principal r
    LEFT JOIN subrubro_primero sp ON r.idrubro = sp.rubro_principal_idrubro
    LEFT JOIN subrubro_segundo ss ON sp.idsubrubro = ss.subrubro_primero_idsubrubro";

// Total de registros sin filtro
$totalQuery = "SELECT COUNT(*) as total $sqlBase";
$totalRecords = $conn->query($totalQuery)->fetch_assoc()['total'];

// Filtro de búsqueda
$where = "";
if (!empty($request['search']['value'])) {
    $search = $conn->real_escape_string($request['search']['value']);
    $whereParts = [];

    foreach ($columns as $col) {
        $whereParts[] = "$col LIKE '%$search%'";
    }

    $where = " WHERE " . implode(" OR ", $whereParts);
}

// Total con filtro
$filteredQuery = "SELECT COUNT(*) as total $sqlBase $where";
$recordsFiltered = $conn->query($filteredQuery)->fetch_assoc()['total'];

// Ordenamiento
$order = "";
if (!empty($request['order'][0]['column'])) {
    $columnIndex = intval($request['order'][0]['column']);
    $columnName = $columns[$columnIndex] ?? null;
    $orderDir = $request['order'][0]['dir'] === 'desc' ? 'DESC' : 'ASC';
    if ($columnName) {
        $order = " ORDER BY $columnName $orderDir";
    }
}

// Paginación
$start = intval($request['start']);
$length = intval($request['length']);
$limit = " LIMIT $start, $length";

// Consulta final
$sql = "SELECT 
            r.idrubro, r.nombre AS nombre_rubro, 
            sp.idsubrubro, sp.nombre AS nombre_subrubro, 
            ss.idsubrubro_seg, ss.nombre AS nombre_subrubro_seg
        $sqlBase
        $where
        $order
        $limit";

$data = [];
$result = $conn->query($sql);
$counter = $start + 1; // Para numerar las filas correctamente

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "num" => $counter++, // Contador para numerar las filas
        "idrubro" => $row['idrubro'],
        "nombre_rubro" => $row['nombre_rubro'],
        "idsubrubro" => $row['idsubrubro'],
        "nombre_subrubro" => $row['nombre_subrubro'],
        "idsubrubro_seg" => $row['idsubrubro_seg'],
        "nombre_subrubro_seg" => $row['nombre_subrubro_seg'],
        "acciones" => '
            <div class="d-flex justify-content-center">
                <button class="btn btn-warning btn-sm mr-2" onclick="editarRubro(' . $row['idrubro'] . ')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm" onclick="confirmarEliminar(' . $row['idrubro'] . ')">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>'
    ];
}

// Respuesta JSON
$response = [
    "draw" => intval($request['draw']),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $recordsFiltered,
    "data" => $data
];

header('Content-Type: application/json');
echo json_encode($response);
?>
