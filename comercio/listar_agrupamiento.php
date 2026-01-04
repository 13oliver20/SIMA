<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Tabla de agrupamientos -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <!-- Título y Botón alineados -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2 class="mb-2 mb-md-0"><i class="fas fa-cogs"></i> Listado de Agrupamientos</h2>
                <button class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#modalRegistrarAgrupamiento">
                    <i class="fas fa-layer-group"></i>Nuevo Agrupamiento
                </button>
            </div>

            <div class="table-responsive">
                <table id="tabla-agrupamiento" class="display" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Código de Etiqueta</th>
                            <th>Agrupamiento</th>
                            <th>Cantidad de Grupos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Estilo personalizado para texto más pequeño en la tabla -->
<style>
    #tabla-agrupamiento th,
    #tabla-agrupamiento td {
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
<?php require_once "modales/modal_registroagrupamiento.php"; ?>

<script>
$(document).ready(function() {
    let tabla = $('#tabla-agrupamiento').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "modules/agrupamiento/cargar_agrupamiento.php", // Ruta al archivo PHP que maneja los datos
            "type": "POST"
        },
        "columns": [
            { "data": "num" },
            { "data": "cod_etiqueta" },
            { "data": "nom_agrupamiento" },
            { "data": "cantidad_grupos" }, // Columna para mostrar la cantidad de grupos
            { "data": "acciones", "orderable": false }
        ],language: {
        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
    },
        responsive: true,
        autoWidth: false,
        dom:
            "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
            "<'row'<'col-12'tr>>" +
            "<'row mt-3'<'col-sm-5'i>>" +
            "Brtip", // Botones de exportación
        "buttons": [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-success'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> Exportar a PDF',
                className: 'btn btn-danger',
                orientation: 'portrait',
                pageSize: 'A4',
                title: 'Listado de Agrupamientos',
                exportOptions: {
                    columns: ':not(:last-child)' // Excluir la columna de acciones
                }
            }
        ]
    });
});

$(document).on('click', '.btnEliminar', function () {
    const id = $(this).data('id');

    Swal.fire({
        title: '¿Estás seguro?',
        text: '¡Esta acción no se puede deshacer!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'modules/agrupamiento/eliminar_agrupamiento.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function (response) {
                    Swal.fire({
                        icon: response.status,
                        title: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#tabla-agrupamiento').DataTable().ajax.reload(null, false);
                    // O si usas variable:
                    // tabla.ajax.reload(null, false);
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo procesar la solicitud.', 'error');
                }
            });
        }
    });
});


</script>
