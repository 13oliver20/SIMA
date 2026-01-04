<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>
<div class="container-fluid">
    <!-- Vigencias de Poder -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <!-- Título y botón alineados -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2 class="mb-2 mb-md-0"><i class="fas fa-list"></i> Listado de Vigencias de Poder</h2>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalVigencia">
                    <i class="fas fa-file-alt"></i> Vigencia de Poder
                </button>
            </div>

            <div class="table-responsive">
                <table id="tabla-vigencias" class="display" width="100%">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Código</th>
                            <th>Grupo</th>
                            <th>Partida Registral</th>
                            <th>Archivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    #tabla-vigencias th,
    #tabla-vigencias td {
        font-size: 0.85rem !important;
        vertical-align: middle;
    }

    div.dataTables_filter {
        text-align: right !important;
    }

    body {
        overflow-x: hidden;
    }
</style>

<!-- Modal -->
<?php include 'modales/modal_registrovigencia.php'; ?>
<?php require_once "includes/footer.php"; ?>

<script>
$(document).ready(function () {
    $('#tabla-vigencias').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "modules/vigencia_poder/cargar_vigencia.php",
            type: "POST"
        },
        columns: [
            { data: "idvigencia_poder" },
            { data: "etiqueta_grupo" },
            { data: "nombre_grupo" },
            { data: "partida_registral" },
            {
                data: "archivo_vigencia",
                render: function (data) {
                    return data 
                        ? `<a href="uploads/vigencia/${data}" target="_blank" class="text-danger" title="Ver PDF"><i class="fas fa-file-pdf"></i></a>`
                        : "-";
                }
            },
            {
                data: "idvigencia_poder",
                orderable: false,
                searchable: false,
                render: function(id) {
                    return `
                        <div class="btn-group" role="group" aria-label="Acciones">
                            <button type="button" class="btn btn-primary btn-sm" onclick="editarVigencia(${id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm btnEliminarVigencia" data-id="${id}" title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        dom: 
          "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
          "<'row'<'col-12'tr>>" +
          "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>"
    });
});

$(document).on('click', '.btnEliminarVigencia', function() {
    var id = $(this).data('id');

    Swal.fire({
        title: '¿Eliminar Vigencia?',
        text: "Esta acción eliminará el registro y su archivo asociado.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'modules/vigencia_poder/eliminar_vigencia.php',
                type: 'POST',
                data: { id: id },
                success: function(respuesta) {
                    if (respuesta === 'success') {
                        Swal.fire('¡Eliminado!', 'La vigencia fue eliminada correctamente.', 'success');
                        $('#tabla-vigencias').DataTable().ajax.reload(null, false);
                    } else if (respuesta === 'not_found') {
                        Swal.fire('Error', 'Registro no encontrado.', 'error');
                    } else {
                        Swal.fire('Error', 'No se pudo eliminar la vigencia.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error en la conexión con el servidor.', 'error');
                }
            });
        }
    });
});

// Función para editar (implementar según necesidad)
function editarVigencia(id) {
    // Ejemplo: Abrir modal, cargar datos, etc.
    console.log("Editar vigencia con ID:", id);
}
</script>
