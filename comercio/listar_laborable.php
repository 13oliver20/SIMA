<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Tarjeta del listado de grupos -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <!-- Título y Botón alineados -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2 class="mb-2 mb-md-0"><i class="fas fa-users"></i> Listado de Grupos</h2>
                <button class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#modalAsignarDias">
                    <i class="fas fa-calendar-plus"></i> Asignar Días
                </button>
            </div>

            <div class="table-responsive">
                <table id="tabla_grupos" class="display" width="100%">
                    <thead>
                        <tr>
                            <th>Nº</th>
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
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los datos se llenarán por AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Estilo personalizado -->
<style>
    #tabla_grupos th,
    #tabla_grupos td {
        font-size: 0.75rem !important;
        vertical-align: middle;
    }

    div.dataTables_filter {
        text-align: right !important;
    }
</style>

<?php require_once "includes/footer.php"; ?>
<?php require "modales/modal_registrodia.php"; ?>

<script>
$(document).ready(function() {
    $('#tabla_grupos').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "modules/laborable/cargar_laborable.php",
            "type": "POST"
        },
        "autoWidth": false,
        "responsive": true,
        columns: [
            { data: 'num' },
            { data: 'etiqueta_grupo' },
            { data: 'nombre_grupo' },
            { data: 'agrupamiento' },
            { data: 'Lunes' },
            { data: 'Martes' },
            { data: 'Miércoles' },
            { data: 'Jueves' },
            { data: 'Viernes' },
            { data: 'Sábado' },
            { data: 'Domingo' },
            { data: 'acciones', orderable: false, searchable: false }
        ],language: {
        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
    },
        "dom":
            "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +  // Longitud y filtro
            "<'row'<'col-12'tr>>" +                     // Tabla
            "<'row mt-3'<'col-sm-5'i>>" +               // Información (sin paginador)
            "Brtip",                                    // Botones, etc.
        "buttons": [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-success',
                title: 'Listado de Grupos',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7,8,9,10] // Excluir columna "acciones" (índice 11)
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

                    var columnCount = doc.content[1].table.body[0].length;
                    var columnWidths = new Array(columnCount).fill('*');
                    doc.content[1].table.widths = columnWidths;

                    doc.content[1].table.body.forEach(function(row) {
                        row.forEach(function(cell) {
                            cell.alignment = 'center';
                        });
                    });
                },
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7,8,9,10] // Excluir columna "acciones"
                }
            }
        ]
    });
});
</script>

