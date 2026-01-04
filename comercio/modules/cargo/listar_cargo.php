<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h1 class="h3 mb-4 text-gray-800">Listado de Cargos</h1>

    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#modalRegistrarCargo">Nuevo Cargo
    </button>
            <div class="table-responsive">
                <table id="tablaCargos" class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>ID Cargo</th>
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
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json"
        }
    });

    // Evento click botón Editar
    $('#tablaCargos tbody').on('click', '.btnEditar', function() {
        var id = $(this).data('id');
        alert('Editar cargo con ID: ' + id);
        // Aquí abrirías un modal para editar el cargo
    });

    // Evento click botón Eliminar
    $('#tablaCargos tbody').on('click', '.btnEliminar', function() {
        var id = $(this).data('id');
        if (confirm('¿Estás seguro de eliminar el cargo con ID ' + id + '?')) {
            $.ajax({
                url: 'modules/cargo/cargo_eliminar.php',
                type: 'POST',
                data: { idcargo: id },
                success: function(response) {
                    var res = JSON.parse(response);
                    if(res.success) {
                        tabla.ajax.reload(null, false); // Recarga sin resetear paginación
                        alert('Cargo eliminado correctamente');
                    } else {
                        alert('Error: ' + res.error);
                    }
                },
                error: function() {
                    alert('Error en la petición para eliminar');
                }
            });
        }
    });
});
</script>
