<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Grupos</title>
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
    <h2><i class="fas fa-users"></i> Listado de Grupos</h2>
     <!-- Botón para abrir el modal -->
    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistrarGrupo">
            <i class="fas fa-users"></i> Registrar Grupo
        </button>
    </div>
    <table id="tabla-grupos" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Nº</th>
                <th>Etiqueta</th>
                <th>Nombre</th>
                <th>Ubicación</th>
                <th>Agrupamiento</th>
                <th>Categoría</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Incluir el modal desde otro archivo PHP -->
<?php include 'modales/modal_registrogrupo.php'; ?>
<?php include 'modales/modal_editargrupo.php'; ?>

<script>
$(document).ready(function() {
    var tabla = $('#tabla-grupos').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "modules/asociaciones/cargar_grupo.php",
            "type": "POST"
        },
        "columns": [
            { "data": "num" },
            { "data": "etiqueta_grupo" },
            { "data": "nombre_grupo" },
            { "data": "ubicacion" },
            { "data": "agrupamiento" },
            { "data": "categoria" },
            { "data": "estado" },
            { "data": "acciones", "orderable": false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        "lengthMenu": [[8, 16, 20, -1], [8, 16, 20, "Todos"]],
        "responsive": true,
        "autoWidth": false,
        "dom": 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-success',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> Exportar a PDF',
                className: 'btn btn-danger',
                orientation: 'landscape',
                pageSize: 'A4',
                title: 'Listado de Grupos',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ]
    });
});

function confirmarEliminar(idgrupo) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Deseas eliminar este grupo?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            var tabla = $('#tabla-grupos').DataTable();
            var paginaActual = tabla.page();
            $.ajax({
                url: 'modules/asociaciones/eliminar_grupo.php',
                type: 'POST',
                data: { idgrupo: idgrupo },
                success: function(response) {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado correctamente',
                            text: 'El grupo ha sido eliminado con éxito.',
                            showConfirmButton: true,
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                    tabla.ajax.reload(null, false);
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo eliminar el grupo.', 'error');
                }
            });
        }
    });
}

// Cargar los datos de un grupo cuando se hace clic en "Editar"
$('#tabla-grupos').on('click','.editar-grupo', function () {
    var grupoId = $(this).data('id'); // Asumiendo que pasas el id del grupo como atributo 'data-idgrupo'
    
    // Realiza la llamada AJAX para obtener los datos del grupo
    $.ajax({
        url: 'modules/asociaciones/get_grupo.php',
        type: 'POST',
        data: { idgrupo: grupoId },
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                $('#edit_idgrupo').val(response.data.idgrupo);
                $('#edit_etiqueta_grupo').val(response.data.etiqueta_grupo);
                $('#edit_nombre_grupo').val(response.data.nombre_grupo);
                $('#edit_ubicacion').val(response.data.ubicacion);
                $('#edit_agrupamiento_idagrupamiento').val(response.data.agrupamiento_idagrupamiento);
                $('#edit_categoria_idcategoria').val(response.data.categoria_idcategoria);
                $('#edit_estado').val(response.data.estado);
                
                $('#modalEditarGrupo').modal('show'); // Abre el modal de edición
            } else {
                Swal.fire('Error', 'No se pudieron cargar los datos del grupo.', 'error');
            }
        },
        error: function () {
            Swal.fire('Error', 'Ocurrió un error al cargar los datos del grupo.', 'error');
        }
    });
});



</script>

</body>
</html>
