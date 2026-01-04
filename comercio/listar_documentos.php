<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-body">

            <!-- Título y botones alineados -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2 class="mb-2 mb-md-0"><i class="fas fa-file-alt"></i> Documentos</h2>
                <div class="d-flex flex-wrap">
                    <button type="button" class="btn btn-primary btn-sm mr-2 mb-2" data-toggle="modal" data-target="#modalRegistrarActa"> 
                        <i class="fas fa-file-alt"></i> Nueva Acta
                    </button>
                    <button type="button" class="btn btn-primary btn-sm mr-2 mb-2" data-toggle="modal" data-target="#modalVigencia">
                        <i class="fas fa-file-alt"></i> Nueva Vigencia
                    </button>
                    <button type="button" class="btn btn-primary btn-sm mr-2 mb-2" data-toggle="modal" data-target="#modalPadron">
                        <i class="fas fa-file-alt"></i> Nuevo Padrón 
                    </button>
                    <button type="button" class="btn btn-primary btn-sm mb-2" data-toggle="modal" data-target="#modalResolucion">
                        <i class="fas fa-file-alt"></i> Nueva Resolución
                    </button>
                </div>
            </div>

            <!-- Tabla -->
            <div class="table-responsive">
                <table id="tablaGrupos" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Código</th>
                            <th class="text-center">Grupo</th>
                            <th class="text-center">Partida Registral</th>
                            <th class="text-center">Fecha Fundación</th>
                            <th class="text-center">Archivo Acta</th>
                            <th class="text-center">Archivo Vigencia</th>
                            <th class="text-center">Archivo Padrón</th>
                            <th class="text-center">Archivo Resolución</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<style>
    #tablaGrupos th,
    #tablaGrupos td {
        font-size: 0.75rem !important;
        vertical-align: middle;
    }

    div.dataTables_filter {
        text-align: right !important;
    }
</style>

<?php require_once "includes/footer.php"; ?>

<!-- Modales -->
<?php 
include 'modales/modal_registroacta.php'; 
include 'modales/modal_registrovigencia.php'; 
include 'modales/modal_registropadron.php'; 
include 'modales/modal_registroresolucion.php'; 
?>

<script>
$(document).ready(function() {
    $('#tablaGrupos').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "modules/documentos/cargar_documento.php",
            type: "POST"
        },
        columns: [
            { data: "num", className: "text-center" },
            { data: "etiqueta_grupo" },
            { data: "nombre_grupo" },
            { data: "partida_registral" },
            { data: "fecha_fundacion", className: "text-center" },
            { data: "archivo_acta", orderable: false, className: "text-center" },
            { data: "archivo_vigencia", orderable: false, className: "text-center" },
            { data: "archivo_padron", orderable: false, className: "text-center" },
            { data: "archivo_gdh", orderable: false, className: "text-center" }
        ],language: {
        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
    },
        responsive: true,
        autoWidth: false,
        dom:
            "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
            "<'row'<'col-12'tr>>" +
            "<'row mt-3'<'col-sm-5'i>>" +
            "Brtip",
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> Exportar a PDF',
                className: 'btn btn-danger btn-sm',
                orientation: 'landscape',
                pageSize: 'A4',
                title: 'Documentos',
                exportOptions: { columns: ':not(:last-child)' }
            }
        ]
    });
});
</script>
