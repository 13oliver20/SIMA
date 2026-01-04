<?php
require_once(__DIR__ . "/../../includes/conexion.php");

// Definir las columnas que se van a seleccionar
$columns = ['g.idgrupo', 'g.etiqueta_grupo', 'g.nombre_grupo', 'g.ubicacion', 'a.nom_agrupamiento AS agrupamiento', 'c.tipo AS categoria', 'g.estado'];

// Consulta para obtener todos los registros ordenados por idgrupo
$query = "SELECT " . implode(", ", $columns) . " 
          FROM grupo g
          JOIN agrupamiento a ON g.agrupamiento_idagrupamiento = a.idagrupamiento
          JOIN categoria c ON g.categoria_idcategoria = c.idcategoria
          ORDER BY g.idgrupo ASC"; // Ordenar por idgrupo de forma ascendente

$result = mysqli_query($conn, $query);

// Preparar los datos para la respuesta
$data = [];
$counter = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "num" => $counter++,  // Número incremental
        "etiqueta_grupo" => $row['etiqueta_grupo'],
        "nombre_grupo" => $row['nombre_grupo'],
        "ubicacion" => $row['ubicacion'],
        "agrupamiento" => $row['agrupamiento'],  // Nombre del agrupamiento
        "categoria" => $row['categoria'],        // Nombre de la categoría
        "estado" => $row['estado'],
        "acciones" => '<div class="d-flex justify-content-center">
                        <button class="btn btn-warning btn-sm mr-2 editar-grupo" data-id="'. $row['idgrupo'] . '">
                    <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="confirmarEliminar(' . $row['idgrupo'] . ')">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>'
    ];
}

// Crear la respuesta en formato JSON
$response = [
    "data" => $data
];

// Devolver la respuesta en JSON
echo json_encode($response);
?>
