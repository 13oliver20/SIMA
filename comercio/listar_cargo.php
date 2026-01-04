<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <!-- Page Heading -->
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Listado de Cargos</h1>
                <button class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#modalRegistrarCargo">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Nuevo Cargo
                </button>
            </div>

            <div class="table-responsive">
                <table id="tablaCargos" class="display" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>N°</th>
                            <th>Tipo de Cargo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data cargada vía AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<!-- Estilo personalizado para texto más pequeño en la tabla -->
<style>
    #tablaCargos th,
    #tablaCargos td {
        font-size: 0.75rem !important;
        vertical-align: middle;
    }

    div.dataTables_filter {
        text-align: right !important;
    }
</style>


<?php
require_once "includes/footer.php"; 
?>

<?php
require_once "modales/modal_registrocargo.php"; 
?>
<?php
require_once "modales/modal_editarcargo.php"; 
?>
<script>
$(document).ready(function() {
    var tabla = $('#tablaCargos').DataTable({
        "ajax": "modules/cargo/cargar_cargo.php",
        "columns": [
            { "title": "ID Cargo" },
            { "title": "Tipo de Cargo" },
            { 
                "title": "Acciones", 
                "orderable": false, 
                "searchable": false 
            }
        ],
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
                title: 'Listado de Socios Asociados',
                exportOptions: { columns: ':not(:last-child)' }
            }
        ]
    });

    // Delegar evento click para eliminar porque los botones se cargan dinámicamente
    $(document).on('click', '.btnEliminar', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'modules/cargo/eliminar_cargo.php',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                '¡Eliminado!',
                                'El cargo fue eliminado correctamente.',
                                'success'
                            );
                            // Recarga DataTable sin perder paginación
                            $('#tablaCargos').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', response.error || 'No se pudo eliminar el cargo.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error en la comunicación con el servidor.', 'error');
                    }
                });
            }
        });
    });

    // Delegar evento click para editar: abre modal y carga datos por AJAX
    $(document).on('click', '.btnEditar', function() {
        const id = $(this).data('id');

        $.ajax({
            url: 'modules/cargo/obtener_cargo.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#edit_idcargo').val(response.data.idcargo);
                    $('#edit_tipo_cargo').val(response.data.tipo_cargo);
                    $('#modalEditarCargo').modal('show');
                } else {
                    Swal.fire('Error', response.error || 'No se pudo obtener datos del cargo', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
            }
        });
    });
});

</script>
