<?php 
require_once(__DIR__ . "/../../includes/conexion.php");

$columns = ['sa.socio_idsocio', 's.dni', 's.nombre', 's.apellido_pat', 's.apellido_mat','s.genero', 'g.nombre_grupo', 'a.nom_agrupamiento', 'c.tipo', 'rp.nombre AS rubro_principal', 'sp1.nombre AS subrubro_primero', 'sp2.nombre AS subrubro_segundo', 'sa.cod_puesto', 'sa.observacion'];

// Consulta para obtener todos los registros sin filtros ni paginación
$query = "SELECT " . implode(", ", $columns) . "
          FROM socio_asociacion sa
          INNER JOIN socio s ON sa.socio_idsocio = s.idsocio
          INNER JOIN grupo g ON sa.grupo_idgrupo = g.idgrupo
          INNER JOIN agrupamiento a ON g.agrupamiento_idagrupamiento = a.idagrupamiento
          INNER JOIN categoria c ON g.categoria_idcategoria = c.idcategoria
          INNER JOIN subrubro_segundo sp2 ON sa.subrubro_segundo_idsubrubro_seg = sp2.idsubrubro_seg
          INNER JOIN subrubro_primero sp1 ON sp2.subrubro_primero_idsubrubro = sp1.idsubrubro
          INNER JOIN rubro_principal rp ON sp1.rubro_principal_idrubro = rp.idrubro";

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
        "genero" => $row['genero'] ?? '',
        "grupo" => $row['nombre_grupo'],
        "agrupamiento" => $row['nom_agrupamiento'],
        "categoria" => $row['tipo'],
        "rubro" => $row['rubro_principal'] . ' - ' . $row['subrubro_primero'],
        "puesto" => $row['cod_puesto'] ?? '-',
        "observacion" => $row['observacion'] ?? '-',
        "acciones" => '<div class="d-flex justify-content-center">
                        <button class="btn btn-warning btn-sm mr-2" onclick="editarSocio(' . $row['socio_idsocio'] . ')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="confirmarEliminar(' . $row['socio_idsocio'] . ')">
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
