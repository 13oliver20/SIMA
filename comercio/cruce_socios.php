<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>
<?php
// Conexión a la base de datos
require_once(__DIR__ . "/includes/conexion.php");
// Verificar conexión
if (!isset($conn) || $conn->connect_error) {
    die("Error: No se pudo conectar a la base de datos.");
}
?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Cruce de Socios -->
    <div class="card shadow mb-4">
        <div class="card-body">

            <!-- Título y filtro -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2 class="mb-2 mb-md-0"><i class="fas fa-users"></i> Cruce de Socios</h2>
                <!-- Aquí puedes añadir un botón si se necesita -->
            </div>

            <!-- Filtros -->
            <form id="filtroForm" class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <select id="grupo_id" class="form-control">
                            <option value="">-- Todos los Grupos --</option>
                            <?php
                            $grupos = $conn->query("SELECT idgrupo, etiqueta_grupo, nombre_grupo FROM grupo ORDER BY nombre_grupo");
                            if ($grupos && $grupos->num_rows > 0) {
                                while ($row = $grupos->fetch_assoc()) {
                                    $idgrupo = $row['idgrupo'];
                                    $etiqueta = htmlspecialchars($row['etiqueta_grupo']);
                                    $nombre = htmlspecialchars($row['nombre_grupo']);
                                    echo "<option value='{$idgrupo}'>{$etiqueta} - {$nombre}</option>";
                                }
                            } else {
                                echo "<option disabled>No hay grupos disponibles</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </form>

            <!-- Tabla responsiva -->
            <div class="table-responsive">
                <table id="tabla-cruce" class="display" width="100%">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>DNI</th>
                            <th>Nombre Completo</th>
                            <th>Cantidad</th>
                            <th>Asociaciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Estilo personalizado -->
<style>
    #tabla-cruce th,
    #tabla-cruce td {
        font-size: 0.75rem !important;
        vertical-align: middle;
    }

    div.dataTables_filter {
        text-align: right !important;
    }
</style>

<?php require_once "includes/footer.php"; ?>


    <script>
        $(document).ready(function() {
    let tabla = $('#tabla-cruce').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "modules/cruce_socios/cargar_cruce.php",
            "type": "POST",
            "data": function(d) {
                d.grupo_id = $('#grupo_id').val(); // Solo enviar grupo_id
            },
            "dataSrc": function (json) {
                // Verifica si hay datos antes de retornar
                if (json.data) {
                    return json.data;
                } else {
                    return [];
                }
            }
        },
        "columns": [
            { "data": "num" },
            { "data": "dni" },
            { "data": "nombre_completo" },
            { "data": "total_asociaciones" },
            { "data": "asociaciones" }
        ],
        responsive: true,
        autoWidth: false,
        dom:
            "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
            "<'row'<'col-12'tr>>" +
            "<'row mt-3'<'col-sm-5'i>>" +
            "Brtip",
        "buttons": [
            { 
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-success'
            },
            { 
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> Exportar a PDF',
                className: 'btn btn-danger'
            }
        ],
        "drawCallback": function(settings) {
            var api = this.api();
            var start = api.settings()[0]._iDisplayStart; // Obtiene el número de registros que ya han sido mostrados
            var counter = start + 1; // Contador para la primera columna

            // Iterar sobre las filas actuales
            api.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                var row = $(cell).closest('tr'); // Obtener la fila actual
                var numAsociaciones = row.find('td').eq(4).text(); // Obtener el texto de la columna de asociaciones
                var num = (numAsociaciones !== '') ? counter++ : ''; // Si tiene asociaciones, contar

                // Si la celda está vacía (parte de una fila combinada), le asignamos el número de la fila anterior
                if (cell.innerHTML === "") {
                    cell.innerHTML = num; // Asignar el número en las celdas vacías
                }
            });
        }
    });

    // Filtrar en tiempo real
    $('#grupo_id').on('change', function() {
        tabla.ajax.reload(null, false); // Recargar sin reiniciar paginación
    });
});

    </script>