<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>
<div class="container-fluid">
    <!-- Padrón de Socios -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <!-- Título y botón alineados -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2 class="mb-2 mb-md-0"><i class="fas fa-list"></i> Listado de Padrón de Socios</h2>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalPadron">
                    <i class="fas fa-file-alt"></i> Padrón de socios
                </button>
            </div>

            <div class="table-responsive">
                <table id="tabla-padron" class="display" width="100%">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Grupo</th>
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
    #tabla-padron th,
    #tabla-padron td {
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
<?php include 'modales/modal_registroPadron.php'; ?>
<?php require_once "includes/footer.php"; ?>

<script>
$(document).ready(function() {
    var tabla = $('#tabla-padron').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "modules/padron_socios/cargar_padron.php",
            type: "POST"
        },
        columns: [
            { data: "idpadron_socios" },
            { data: "nombre_grupo" }, 
            { 
                data: "archivo_padron",
                render: function(data) {
                    return data 
                        ? `<a href="uploads/padron/${data}" target="_blank" class="text-danger" title="Ver PDF">
                            <i class="fas fa-file-pdf"></i>
                           </a>` 
                        : "-";
                }
            },
            { 
                data: "idpadron_socios",
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `
                        <div class="btn-group" role="group" aria-label="Acciones">
                            <button type="button" class="btn btn-primary btn-sm" onclick="editarPadron(${data})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm btn-eliminar" data-id="${data}" title="Eliminar">
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
    });

    // Evento eliminar delegado
    $('#tabla-padron tbody').on('click', '.btn-eliminar', function() {
        var padron_id = $(this).data('id');
        var fila = $(this).closest('tr');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esta acción!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'modules/padron_socios/eliminar_padron.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { padron_id: padron_id },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire(
                                'Eliminado!',
                                response.message,
                                'success'
                            );
                            tabla.row(fila).remove().draw();
                        } else {
                            Swal.fire(
                                'Error',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Error',
                            'Error en la petición AJAX: ' + error,
                            'error'
                        );
                    }
                });
            }
        });
    });
});

// Función para editar (a implementar según tu necesidad)
function editarPadron(id) {
    console.log("Editar padrón con ID:", id);
}
</script>
