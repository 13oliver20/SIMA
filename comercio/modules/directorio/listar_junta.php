<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Junta Directiva</title>
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
        <h2>Listado de Junta Directiva</h2>
        <table id="tabla_junta" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>DNI</th>
                    <th>Nombre</th>
                    <th>Etiqueta Grupo</th>
                    <th>Grupo</th>
                    <th>Cargo</th>
                    <th>Celular</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            let tablaJunta = $('#tabla_junta').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: 'modules/directorio/cargar_directorio.php',
                    type: 'POST'
                },
                columns: [
                    { data: 'num', title: '#' },
                    { data: 'dni_socio', title: 'DNI' },
                    { data: 'nombre_socio', title: 'Nombre' },
                    { data: 'etiqueta_grupo', title: 'Etiqueta Grupo' }, // Nueva columna
                    { data: 'nombre_grupo', title: 'Grupo' },
                    { data: 'nombre_cargo', title: 'Cargo' },
                    { data: 'celular', title: 'Celular' },
                    { data: 'acciones', title: 'Acciones', orderable: false, searchable: false }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                }
            });
        });
    </script>
</body>
</html>
