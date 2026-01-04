<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Agrupamientos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1100px;
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

<div class="container">
    <h2><i class="fas fa-cogs"></i> Listado de Agrupamientos</h2>
    <table id="tabla-agrupamiento" class="display" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Código de Etiqueta</th>
                <th>Agrupamiento</th>
                <th>Cantidad de Grupos</th> <!-- Nueva columna -->
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
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
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "lengthMenu": [[5, 10, 15, -1], [5, 10, 15, "Todos"]],
        "responsive": true,
        "autoWidth": false,
        "dom": 'Bfrtip', // Botones de exportación
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

// Función para eliminar agrupamiento con SweetAlert2
function confirmarEliminar(id) {
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
            $.post("modules/agrupamiento/eliminar_agrupamiento.php", { id: id }, function() {
                Swal.fire("¡Eliminado!", "El agrupamiento ha sido eliminado.", "success");
                $('#tabla-agrupamiento').DataTable().ajax.reload();
            });
        }
    });
}
</script>

</body>
</html>
