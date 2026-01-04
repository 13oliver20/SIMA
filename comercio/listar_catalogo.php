<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>

<div class="container-fluid mt-4">
    <!-- Tabla de Rubros -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <!-- Título y Botón -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <h2 class="mb-2 mb-md-0"><i class="fas fa-th-list"></i> Catálogo de Rubros</h2>
    </div>
            <div class="table-responsive">
                <table id="catalogoTable" class="display" width="100%">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>ID Rubro</th>
                            <th>Nombre Rubro</th>
                            <th>Nombre Subrubro</th>
                            <th>Nombre Subrubro Segundo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Estilo personalizado para texto más pequeño -->
<style>
    #catalogoTable th,
    #catalogoTable td {
        font-size: 0.75rem !important;
        vertical-align: middle;
    }

    div.dataTables_filter {
        text-align: right !important;
    }
</style>

<?php require_once "includes/footer.php"; ?>


<script>
$(document).ready(function() {
    let tabla = $('#catalogoTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "modules/catalogo/cargar_catalogo.php", // Asegúrate de que esta ruta esté correcta
            "type": "POST"
        },
        "columns": [
            { "data": "num" }, // Columna para el enumerador
            { "data": "idsubrubro_seg" },
            { "data": "nombre_rubro" },
            { "data": "nombre_subrubro" },
            { "data": "nombre_subrubro_seg" },
            { "data": "acciones", "orderable": false }
        ],
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
});

// Función para eliminar rubro con SweetAlert2
function confirmarEliminar(idrubro) {
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
            $.post("modules/catalogo/eliminar_rubro.php", { idrubro: idrubro }, function() {
                Swal.fire("¡Eliminado!", "El rubro ha sido eliminado.", "success");
                $('#catalogoTable').DataTable().ajax.reload();
            });
        }
    });
}

// Función para editar rubro
function editarRubro(idrubro) {
    Swal.fire({
        title: 'Editar Rubro',
        text: `Función de edición para ID Rubro: ${idrubro}`,
        icon: 'info',
        confirmButtonText: 'Cerrar'
    });

    // Puedes redirigir o abrir un modal aquí si lo deseas
    // window.location.href = `editar_rubro.php?id=${idrubro}`;
}
</script>

</body>
</html>
