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
        h2 {
            margin-bottom: 20px;
        }
        table.dataTable {
            font-size: clamp(12px, 1vw, 14px);
            width: 100% !important;
        }
        .btn-group {
            margin-top: 10px;
        }
        .btn {
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        table.dataTable tbody td {
    font-size: 12px; /* Ajusta el tamaño de la fuente de la tabla */
}


    </style>
</head>
<body>

<div class="container">
    <h2><i class="fas fa-users"></i> Listado de Grupos</h2>

    <table id="tabla_grupos" class="display" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Etiqueta</th>
                <th>Nombre</th>
                <th>Agrupamiento</th> 
                <th>Lunes</th>
                <th>Martes</th>
                <th>Miércoles</th>
                <th>Jueves</th>
                <th>Viernes</th>
                <th>Sábado</th>
                <th>Domingo</th>
            </tr>
        </thead>
        <tbody>
            <!-- Los datos se llenarán por AJAX -->
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    $('#tabla_grupos').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "modules/laborable/cargar_laborable.php",
            "type": "POST"
        },
        "autoWidth": true,  
        "responsive": true, 
        "columns": [
            { "data": "num" },
            { "data": "etiqueta_grupo" },
            { "data": "nombre_grupo" },
            { "data": "agrupamiento" },
            { "data": "Lunes" },
            { "data": "Martes" },
            { "data": "Miércoles" },
            { "data": "Jueves" },
            { "data": "Viernes" },
            { "data": "Sábado" },
            { "data": "Domingo" }
        ],
        language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
        dom: 'Bfrtip', 
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-success',
                title: 'Listado de Grupos',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> Exportar a PDF',
                className: 'btn btn-danger',
                title: 'Listado de Grupos',
                orientation: 'landscape',
                pageSize: 'A4',
                customize: function(doc) {
                    doc.defaultStyle.fontSize = 8;
                    doc.styles.tableHeader.fontSize = 10;
                    doc.pageMargins = [20, 20, 20, 20];

                    // Ajuste automático de columnas para que ocupen todo el ancho de la página
                    var columnCount = doc.content[1].table.body[0].length;
                    var columnWidths = new Array(columnCount).fill('*'); // Distribuye el ancho equitativamente
                    doc.content[1].table.widths = columnWidths;

                    // Evita que el texto se corte en las columnas
                    doc.content[1].table.body.forEach(function(row) {
                        row.forEach(function(cell) {
                            cell.alignment = 'center'; // Centra el texto en las celdas
                        });
                    });
                },
                exportOptions: {
                    columns: ':visible'
                }
            }
        ]
    });
});
</script>

</body>
</html>
