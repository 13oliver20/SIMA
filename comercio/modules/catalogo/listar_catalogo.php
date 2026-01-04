<?php
require_once(__DIR__ . "/../../includes/conexion.php");
require_once(__DIR__ . "/../../includes/header.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Rubros</title>

    <!-- DataTables CSS + Buttons -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <!-- FontAwesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        table.dataTable {
            font-size: clamp(12px, 1vw, 14px);
            width: 100% !important;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2><i class="fas fa-th-list"></i> Catálogo de Rubros</h2>
    <div class="card shadow">
        <div class="card-body">
            <table id="catalogoTable" class="display nowrap" style="width:100%">
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

<!-- jQuery y DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "lengthMenu": [[5, 10, 15, -1], [5, 10, 15, "Todos"]],
        "responsive": true,
        "autoWidth": false,
        "dom": 'Bfrtip',
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
                title: 'Listado de Rubros',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
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
