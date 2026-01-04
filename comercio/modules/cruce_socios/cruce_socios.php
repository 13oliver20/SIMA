<?php
// Conexión a la base de datos
require_once(__DIR__ . "/../../includes/conexion.php");
// Verificar conexión
if (!isset($conn) || $conn->connect_error) {
    die("Error: No se pudo conectar a la base de datos.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cruce de Socios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        table.dataTable {
            font-size: clamp(12px, 1vw, 14px);
            width: 100% !important;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2><i class="fas fa-users"></i> Cruce de Socios</h2>
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

        <!-- Tabla -->
        <table id="tabla-cruce" class="display" style="width:100%">
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
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        "dom": 'Bfrtip',
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

</body>
</html>
