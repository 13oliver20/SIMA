<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Junta Directiva</title>
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
<div class="container">
    <h2><i class="fas fa-users-cog"></i> Listado de Junta Directiva</h2>
    <div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistrarJunta">
    <i class="fas fa-file-alt"></i> Registrar Junta
</button>
</div>
    <table id="tabla-junta-directiva" class="display" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>DNI</th>
                <th>Nombre Completo</th>
                <th>Grupo</th>
                <th>Cargo</th>
                <th>Fecha - Inicio</th>
                <th>Fecha - Fin</th>
                <th>Celular</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Incluir modal -->
<?php include 'modales/modal_registrojunta.php'; ?>

<script>
$(document).ready(function() {
    let tabla = $('#tabla-junta-directiva').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "modules/junta_directiva/cargar_junta.php", // Ruta del script PHP
            "type": "POST"
        },
        "columns": [
            { "data": "num" },
            { "data": "dni_socio" },
            { "data": "nombre_socio" },
            { "data": "nombre_grupo" },
            { "data": "nombre_cargo" },
            { "data": "fecha_inicio" },
            { "data": "fecha_fin" },
            { "data": "celular" },
            { "data": "acciones", "orderable": false }
        ],
        language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
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
                title: 'Listado de Junta Directiva',
                exportOptions: {
                    columns: ':not(:last-child)' // Excluir la columna de acciones
                }
            }
        ]
    });
});

// Función para eliminar un registro con SweetAlert2
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
            $.post("modules/junta_directiva/eliminar_junta_directiva.php", { id: id }, function() {
                Swal.fire("¡Eliminado!", "El registro ha sido eliminado.", "success");
                $('#tabla-junta-directiva').DataTable().ajax.reload();
            });
        }
    });
}
</script>

</body>
</html>
