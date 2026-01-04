<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Encabezado y Tabla -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <!-- Título principal -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2 class="mb-2 mb-md-0"><i class="fas fa-layer-group"></i> Listado de Grupos</h2>
                <!-- Aquí podrías agregar un botón de registrar si lo necesitas -->
            </div>

            <!-- Tabla -->
            <div class="table-responsive">
                <table id="tabla-grupos" class="display" width="100%">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Etiqueta</th>
                            <th>Nombre del Grupo</th>
                            <th>Agrupamiento</th>
                            <th>Cantidad de Socios</th>
                            <th>Padrones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Estilos personalizados -->
<style>
    #tabla-grupos th,
    #tabla-grupos td {
        font-size: 0.75rem !important;
        vertical-align: middle;
    }

    div.dataTables_filter {
        text-align: right !important;
    }

    body {
        overflow-x: hidden;
    }
</style>

<?php require_once "includes/footer.php"; ?>


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
            { "data": "padron" },
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
                title: 'CANTIDAD DE SOCIOS POR ASOCIACION(GRUPOS)',
                exportOptions: { columns: ':not(:last-child)' }
            }
        ]
    });
});
</script>

