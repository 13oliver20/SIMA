<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Grupos</title>
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
        <h2><i class="fas fa-file-alt"></i> Documentos</h2>

        <!-- Fila de botones alineados a la izquierda -->
        <div class="d-flex justify-content-start gap-2 mb-3">
            <!-- Botón Nuevo Socio -->
            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modalRegistrarActa"> 
                <i class="fas fa-file-alt"></i> Nueva Acta
            </button>

            <!-- Botón Registrar Grupo -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalVigencia">
                <i class="fas fa-file-alt"></i> Nueva Vigencia
            </button>

            <!-- Botón Nuevo Socio -->
            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modalPadron">
                <i class="fas fa-file-alt"></i> Nuevo Padron 
            </button>

            <!-- Botón Registrar Grupo -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalResolucion">
                <i class="fas fa-file-alt"></i> Nueva Resolucion
            </button>
        </div>

        <!-- Tabla -->
        <table id="tablaGrupos" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Grupo</th>
                    <th>Partida Registral</th>
                    <th>Fecha Fundación</th>
                    <th>Archivo Acta</th>
                    <th>Archivo Vigencia</th>
                    <th>Archivo Padrón</th>
                    <th>Archivo Resolución</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Incluir el modal desde otro archivo PHP -->
    <?php include 'modales/modal_registroacta.php'; ?>
    <?php include 'modales/modal_registrovigencia.php'; ?>
    <?php include 'modales/modal_registropadron.php'; ?>
    <?php include 'modales/modal_registroresolucion.php'; ?>

    <script>
        $(document).ready(function() {
            $('#tablaGrupos').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "modules/documentos/cargar_documento.php",
                    "type": "POST"
                },
                "columns": [
                    { "data": "num" },
                    { "data": "etiqueta_grupo" },
                    { "data": "nombre_grupo" },
                    { "data": "partida_registral" },
                    { "data": "fecha_fundacion" },
                    { "data": "archivo_acta", "orderable": false, "className": "text-center" },
                    { "data": "archivo_vigencia", "orderable": false, "className": "text-center" },
                    { "data": "archivo_padron", "orderable": false, "className": "text-center" },
                    { "data": "archivo_gdh", "orderable": false, "className": "text-center" }
                ],
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros por página",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando página _PAGE_ de _PAGES_",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(filtrado de _MAX_ registros en total)",
                    "search": "Buscar:",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                }
            });
        });
    </script>

</body>
</html>
