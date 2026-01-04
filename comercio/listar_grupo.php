<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Tabla de grupos -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <!-- Título y Botón alineados -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2><i class="fas fa-users"></i> Listado de Grupos</h2>
                <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#modalRegistrarGrupo">
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
    </div>
</div>

<style>
    #tabla-grupos th,
    #tabla-grupos td {
        font-size: 0.8rem !important;
        vertical-align: middle;
    }

    div.dataTables_filter {
        text-align: right !important;
    }
</style>


<?php include 'includes/footer.php'; ?>
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
        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
    },
        "responsive": true,
        "autoWidth": false,
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
