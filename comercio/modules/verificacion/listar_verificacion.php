<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Verificación de Socios</title>
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
        <h2><i class="fas fa-user-check"></i> Listado de Verificación de Socios</h2>
        <table id="tabla_verificacion" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>#</th> <!-- Columna para el contador -->
                    <th>DNI</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Grupo</th>
                    <th>Primera Verificación</th>
                    <th>Fecha</th>
                    <th>Segunda Verificación</th>
                    <th>Fecha</th>
                    <th>Tercera Verificación</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#tabla_verificacion').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "modules/verificacion/cargar_verificacion.php",
                    "type": "POST"
                },
                "columns": [
                    { "data": "contador" }, // Muestra el contador en la primera columna
                    { "data": "dni" },
                    { "data": "nombre" },
                    { "data": "apellidos" },
                    { "data": "grupo" },
                    { "data": "primera_verificacion" },
                    { "data": "primera_fecha" },
                    { "data": "segunda_verificacion" },
                    { "data": "segunda_fecha" },
                    { "data": "tercera_verificacion" },
                    { "data": "tercera_fecha" },
                    { "data": "estado" }
                ],
                "language": { "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json" },
                "dom": 'Bfrtip', // Definimos que los botones estén disponibles
                "buttons": [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                        title: 'Listado_Verificación_Socios',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> Exportar a PDF',
                        title: 'Listado_Verificación_Socios',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        className: 'btn btn-danger'
                    }
                ]
            });
        });
    </script>
</body>
</html>
