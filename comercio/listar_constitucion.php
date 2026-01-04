<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-body">

            <!-- Título y botón alineados -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2 class="mb-2 mb-md-0"><i class="fas fa-file-alt"></i> Listado de Actas de Constitución</h2>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalRegistrarActa">
                    <i class="fas fa-file-alt"></i> Registrar Acta
                </button>
            </div>

            <!-- Tabla -->
            <div class="table-responsive">
                <table id="tabla-actas" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">Nº</th>
                            <th>Código</th>
                            <th>Grupo</th>
                            <th class="text-center">Fecha de Fundación</th>
                            <th class="text-center">Archivo</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<style>
    #tabla-actas th,
    #tabla-actas td {
        font-size: 0.85rem !important;
        vertical-align: middle;
    }
    div.dataTables_filter {
        text-align: right !important;
    }
</style>

<!-- Modal Registrar Acta -->
<?php include_once "modales/modal_registroacta.php"; ?>

<?php require_once "includes/footer.php"; ?>

<script>
$(document).ready(function() {
    const tabla = $('#tabla-actas').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "modules/acta_constitucion/cargar_constitucion.php",
            type: "POST"
        },
        columns: [
            { data: "num", className: "text-center" },
            { data: "codigo" },
            { data: "grupo" },
            { data: "fecha_fundacion", className: "text-center" },
            { data: "archivo", orderable: false, searchable: false, className: "text-center" },
            { data: "acciones", orderable: false, searchable: false, className: "text-center" }
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
    });

    // Evento para eliminar acta
    $('#tabla-actas tbody').on('click', '.btnEliminar', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: '¿Eliminar Acta?',
            text: "Esta acción eliminará el acta y su archivo PDF.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'modules/acta_constitucion/eliminar_constitucion.php',
                    type: 'POST',
                    data: { id: id },
                    success: function(respuesta) {
                        if (respuesta === 'success') {
                            Swal.fire(
                                '¡Eliminado!',
                                'El acta fue eliminada correctamente.',
                                'success'
                            );
                            tabla.ajax.reload(null, false); // recarga sin resetear paginación
                        } else if (respuesta === 'not_found') {
                            Swal.fire('Error', 'Acta no encontrada.', 'error');
                        } else {
                            Swal.fire('Error', 'No se pudo eliminar el acta.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error en la conexión con el servidor.', 'error');
                    }
                });
            }
        });
    });

    // Evento para editar acta (puedes agregar funcionalidad real)
    $('#tabla-actas tbody').on('click', '.btnEditar', function() {
        const id = $(this).data('id');
        console.log("Editar acta con ID:", id);
        // Aquí podrías abrir modal con datos para editar
    });
});
</script>
