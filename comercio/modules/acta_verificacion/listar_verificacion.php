<?php
// Requiere la conexión a la base de datos
require_once(__DIR__ . "/../../includes/conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Grupos</title>
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
        table.dataTable tbody td {
            font-size: 10.5px;
        }
        
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2><i class="fas fa-file-alt"></i> Listado de Actas de Verificación</h2>
        <table id="tabla_grupos" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Nº</th> <!-- Agregado contador -->
                    <th>Nombre Grupo</th>
                    <th>Primera Verificación</th>
                    <th>Fecha</th>
                    <th>Archivo</th>
                    <th>Segunda Verificación</th>
                    <th>Fecha</th>
                    <th>Archivo</th>
                    <th>Tercera Verificación</th>
                    <th>Fecha</th>
                    <th>Archivo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <script>
        $(document).ready(function() {
            $('#tabla_grupos').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "modules/acta_verificacion/cargar_verificacion.php",
                    "type": "POST"
                },
                "columns": [
                    { "data": "contador" }, // Contador
                    { "data": "nombre_grupo" },
                    { "data": "verificacion_1" },
                    { "data": "fecha_1" },
                    { "data": "archivo_1" },
                    { "data": "verificacion_2" },
                    { "data": "fecha_2" },
                    { "data": "archivo_2" },
                    { "data": "verificacion_3" },
                    { "data": "fecha_3" },
                    { "data": "archivo_3" },
                    { "data": "acciones" }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },

                "columnDefs": [
                    { "orderable": false, "targets": [0, 11] } // Evita ordenar por contador y acciones
                ]
            });
        });
    </script>
</body>
</html>
