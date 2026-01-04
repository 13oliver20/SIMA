<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Grupos</title>
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
        table.dataTable td:nth-child(5),
        table.dataTable td:nth-child(6) {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fas fa-layer-group"></i> Listado de Grupos</h2>
    <table id="tabla-grupos" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Nº</th>
                <th>Etiqueta</th>
                <th>Nombre del Grupo</th>
                <th>Agrupamiento</th>
                <th>Cantidad de Socios</th>
                <th>Padron</th>
            </tr>
        </thead>
    </table>
</div>

<script>
$(document).ready(function() {
    $('#tabla-grupos').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "modules/reportes/cargar_cantidad.php",
            "type": "POST"
        },
        "columns": [
            { "data": "num" },
            { "data": "etiqueta_grupo" },
            { "data": "nombre_grupo" },
            { "data": "nom_agrupamiento" },
            { "data": "cantidad_socios" },
            { "data": "padron" }
        ],
        "dom": 'frtip',  // Aquí se eliminó la 'B' para quitar los botones de exportación
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "lengthMenu": [[8, 10, 15, -1], [8, 10, 15, "Todos"]],
        "responsive": true,
        "autoWidth": false
    });
});
</script>

</body>
</html>
