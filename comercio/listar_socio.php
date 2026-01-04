<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Tabla de socios -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <!-- Título y Botón alineados -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2 class="mb-2 mb-md-0"><i class="fas fa-users"></i> Listado de Socios</h2>
                <button class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#modalRegistrarSocio">
                    <i class="fas fa-user-plus"></i> Nuevo Socio
                </button>
            </div>
            <div class="table-responsive">
                <table id="tabla-socios" class="display" width="100%">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>DNI</th>
                            <th>Nombre</th>
                            <th>Apellido Paterno</th>
                            <th>Apellido Materno</th>
                            <th>Género</th>
                            <th>Departamento</th>
                            <th>Provincia</th>
                            <th>Distrito</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    #tabla-socios th,
    #tabla-socios td {
        font-size: 0.8rem !important;
        vertical-align: middle;
    }

    div.dataTables_filter {
        text-align: right !important;
    }
</style>

<?php require_once "includes/footer.php"; ?>

<!-- Modales -->
<?php include_once "modales/modal_registrosocio.php"; ?>
<?php include_once "modales/modal_editarsocio.php"; ?>

<!-- Scripts -->
<script>
$(document).ready(function() {
    var tabla = $('#tabla-socios').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "modules/socio/cargar_socio.php",
            type: "POST"
        },
        columns: [
            { data: "num" },
            { data: "dni" },
            { data: "nombre" },
            { data: "apellido_pat" },
            { data: "apellido_mat" },
            { data: "genero" },
            { data: "departamento" },
            { data: "provincia" },
            { data: "distrito" },
            { data: "acciones", orderable: false }
        ],
        language: {
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
                title: 'Listado de Socios',
                exportOptions: { columns: ':not(:last-child)' }
            }
        ]
    });

    // Eliminar socio
    $('#tabla-socios').on('click', '.eliminar-socio', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará al socio permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'modules/socio/eliminar_socio.php',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Eliminado', response.message, 'success');
                            tabla.ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Error en la conexión con el servidor.', 'error');
                    }
                });
            }
        });
    });

    // Editar socio
    $('#tabla-socios').on('click', '.editar-socio', function () {
        var id = $(this).data('id');
        $.ajax({
            url: 'modules/socio/get_socio.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#edit-idsocio').val(response.data.idsocio);
                    $('#edit-dni').val(response.data.dni);
                    $('#edit-nombre').val(response.data.nombre);
                    $('#edit-apellido_pat').val(response.data.apellido_pat);
                    $('#edit-apellido_mat').val(response.data.apellido_mat);
                    $('#edit-genero').val(response.data.genero);
                    $('#edit-departamento').val(response.data.departamento);
                    $('#edit-provincia').val(response.data.provincia);
                    $('#edit-distrito').val(response.data.distrito);
                    $('#modalEditarSocio').modal('show');
                } else {
                    Swal.fire('Error', 'No se pudo cargar los datos del socio.', 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Error en la conexión con el servidor.', 'error');
            }
        });
    });
});
</script>
