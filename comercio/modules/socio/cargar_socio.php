<?php 
require_once(__DIR__ . "/../../includes/conexion.php");

$columns = ['idsocio', 'dni', 'nombre', 'apellido_pat', 'apellido_mat', 'genero', 'departamento', 'provincia', 'distrito'];

// Consulta para obtener todos los registros sin filtros ni paginación
$query = "SELECT " . implode(", ", $columns) . " FROM socio";
$result = mysqli_query($conn, $query);

$data = [];
$counter = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "num" => $counter++,
        "dni" => $row['dni'],
        "nombre" => $row['nombre'],
        "apellido_pat" => $row['apellido_pat'],
        "apellido_mat" => $row['apellido_mat'] ?? '',
        "genero" => $row['genero'],
        "departamento" => $row['departamento'],
        "provincia" => $row['provincia'],
        "distrito" => $row['distrito'] ?? '-',
        "acciones" => '<div class="d-flex justify-content-center">
                <button class="btn btn-warning btn-sm mr-2 editar-socio" data-id="' . $row['idsocio'] . '">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm eliminar-socio" data-id="' . $row['idsocio'] . '">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>'

    ];
}

$response = [
    "data" => $data
];

echo json_encode($response);
?>
