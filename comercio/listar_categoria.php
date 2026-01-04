<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Tabla de categorías -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <!-- Título y Botón alineados -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2 class="mb-2 mb-md-0"><i class="fas fa-cogs"></i> Listado de Categorías</h2>
                <button class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#modalRegistrarCategoria">
                    <i class="fas fa-plus"></i> Nueva Categoría
                </button>
            </div>

            <div class="table-responsive">
                <table id="tabla-categorias" class="display" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
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
    #tabla-categorias th,
    #tabla-categorias td {
        font-size: 0.75rem !important;
        vertical-align: middle;
    }

    div.dataTables_filter {
        text-align: right !important;
    }
</style>

<?php require_once "includes/footer.php"; ?>
<?php require_once "modales/modal_registrocategoria.php"; ?>

<script>
$(document).ready(function () {
    $('#tabla-categorias').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "modules/categoria/cargar_categoria.php", // Ruta al archivo PHP
            type: "POST"
        },
        columns: [
            { data: "num" },
            { data: "tipo" },
            { data: "acciones", orderable: false }
        ],language: {
        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
    },
        responsive: true,
        autoWidth: false,
        dom: 'Bfrtip', // Botones de exportación
        buttons: [
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
                title: 'Listado de Categorías',
                exportOptions: {
                    columns: ':not(:last-child)' // Excluir la columna de acciones
                }
            }
        ]
    });
});

function confirmarEliminar(idcategoria) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡Esta acción no se puede deshacer!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'modules/categoria/eliminar_categoria.php',
                type: 'POST',
                data: { idcategoria: idcategoria },  // aquí debe llamarse idcategoria
                dataType: 'json',
                success: function (response) {
                    Swal.fire({
                        icon: response.status,
                        title: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#tabla-categorias').DataTable().ajax.reload(null, false);
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al conectar con el servidor.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }
    });
}

</script>
