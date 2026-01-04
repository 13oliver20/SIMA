<?php
require_once(__DIR__ . "/../../includes/conexion.php");

if ($conn->connect_error) {
    die(json_encode(['error' => 'Error de conexión a la base de datos']));
}

$sql = "SELECT idcargo, tipo_cargo FROM cargo";
$result = $conn->query($sql);

$data = [];
$contador = 1; // Inicializa el enumerador

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $acciones = '
            <div class="d-flex justify-content-center" style="gap: 0.5rem;">
                <button class="btnEditar btn btn-warning btn-sm" data-id="'.$row['idcargo'].'" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btnEliminar btn btn-danger btn-sm" data-id="'.$row['idcargo'].'" title="Eliminar">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        ';
        $data[] = [
            $contador++,      // Enumerador en lugar de idcargo
            $row['tipo_cargo'],
            $acciones
        ];
    }
    $result->free();
}

echo json_encode([
    "data" => $data
]);

$conn->close();
?>
