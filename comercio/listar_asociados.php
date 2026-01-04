<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Tabla de asociados -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <!-- Título y Botón alineados -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2 class="mb-2 mb-md-0"><i class="fas fa-users"></i> Lista de Asociados</h2>
                <!-- Botón para abrir el modal -->
                <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#modalAsociarSocio">
                    <i class="fas fa-user-plus"></i> Asociar Persona
                </button>
            </div>
            <div class="table-responsive">
                <table id="tabla-socios-asociados" class="display" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">Nº</th>
                            <th class="text-center">DNI</th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Apellido Paterno</th>
                            <th class="text-center">Apellido Materno</th>
                            <th class="text-center" style="width: 30px;">G</th>
                            <th class="text-center">Grupo</th>
                            <th class="text-center">Agrupamiento</th>
                            <th class="text-center">Categoría</th>
                            <th class="text-center">Código Puesto</th>
                            <th class="text-center">Subrubro</th>
                            <th class="text-center" style="width: 120px;">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    #tabla-socios-asociados th,
    #tabla-socios-asociados td {
        font-size: 0.8rem !important;
        vertical-align: middle;
    }
    div.dataTables_filter {
        text-align: right !important;
    }
</style>

<?php require_once "includes/footer.php"; ?>
<?php require_once "modales/modal_registroasociado.php"; ?>

<script>
$(document).ready(function() {
    var tablaSociosAsociados = $('#tabla-socios-asociados').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "modules/socio_asociacion/cargar_asociados.php",
            type: "POST"
        },
        columns: [
            { data: "num" },
            { data: "dni" },
            { data: "nombre" },
            { data: "apellido_pat" },
            { data: "apellido_mat" },
            { data: "genero" },
            { data: "grupo" },
            { data: "agrupamiento" },
            { data: "categoria" },
            { data: "puesto" },
            { data: "rubro" },
            { 
                data: "acciones", 
                orderable: false, 
                searchable: false,
                className: 'text-center'
            }
        ],language: {
        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
    },
        responsive: true,
        autoWidth: false,
        dom:
            "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
            "<'row'<'col-12'tr>>" +
            "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>" +
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
                title: 'Listado de Socios Asociados',
                exportOptions: { columns: ':not(:last-child)' }
            }
        ]
    });

    // Confirmar eliminar socio asociado
    window.confirmarEliminar = function(idSocio) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará al socio de la asociación.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('modules/socio_asociacion/eliminar_asociado.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: idSocio })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Eliminado!', 'El socio fue eliminado correctamente.', 'success');
                        tablaSociosAsociados.ajax.reload(null, false);
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo eliminar el socio.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Hubo un problema al conectar con el servidor.', 'error');
                });
            }
        });
    };
});
</script>
