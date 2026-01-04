<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>
<div class="container-fluid">
    <!-- Resoluciones GDH -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <!-- Título y Botón alineados -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2 class="mb-2 mb-md-0"><i class="fas fa-list"></i> Listado de Resoluciones</h2>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalResolucion">
                    <i class="fas fa-file-alt"></i> Resolución GDH
                </button>
            </div>

            <div class="table-responsive">
                <table id="tabla-resoluciones" class="display" width="100%">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Código</th>
                            <th>Grupo</th>
                            <th>Nº Resolución GDH</th>
                            <th>Fecha Emision</th>
                            <th>Archivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    #tabla-resoluciones th,
    #tabla-resoluciones td {
        font-size: 0.85rem !important;
        vertical-align: middle;
    }

    div.dataTables_filter {
        text-align: right !important;
    }

    body {
        overflow-x: hidden;
    }
</style>

<!-- Incluir modal -->
<?php include 'modales/modal_registroresolucion.php'; ?>
<?php require_once "includes/footer.php"; ?>

<script>
$(document).ready(function() {
    let tabla = $('#tabla-resoluciones').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "modules/resolucion_gdh/cargar_resoluciones.php",
            "type": "POST"
        },
        "columns": [
            { "data": "idresolucion_gdh" },
            { "data": "etiqueta_grupo" },
            { "data": "nombre_grupo" },
            { "data": "num_resolucion" },
            { "data": "fecha_emision" },
            { 
                "data": "archivo_gdh",
                "render": function(data) {
                    return data ? `<a href="uploads/resolucion/${data}" target="_blank">
                                        <i class="fas fa-file-pdf text-danger"></i>
                                   </a>` : "-";
                }
            },
            { 
                "data": "idresolucion_gdh",  
                "render": function(data) {
                    return `
                        <div class="btn-group">
                            <button class="btn btn-primary btn-sm" onclick="editarResolucion(${data})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="confirmarEliminar(${data})">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        responsive: true,
        autoWidth: false,
        dom:
    "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
    "<'row'<'col-12'tr>>" +
    "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-success',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> Exportar a PDF',
                className: 'btn btn-danger',
                orientation: 'portrait',
                pageSize: 'A4',
                title: 'Listado de Junta Directiva',
                exportOptions: { columns: ':not(:last-child)' }
            }
        ]
    });

    // Hacer disponible globalmente la función de eliminar
    window.confirmarEliminar = function(id) {
        Swal.fire({
            title: "¿Estás seguro?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("modules/resolucion_gdh/eliminar_resolucion.php", { id: id }, function() {
                    Swal.fire("¡Eliminado!", "La resolución ha sido eliminada.", "success");
                    $('#tabla-resoluciones').DataTable().ajax.reload();
                });
            }
        });
    };
});
</script>
