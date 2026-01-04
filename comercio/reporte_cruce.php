<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Cruce de Socios -->
    <div class="card shadow mb-4">
        <div class="card-body">

            <!-- Título -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2 class="mb-2 mb-md-0"><i class="fas fa-users"></i> Cruce de Socios (por Grupos)</h2>
            </div>

            <!-- Tabla responsiva -->
            <div class="table-responsive">
                <table id="tabla-cruce" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="text-align:center;">Nº</th>
                            <th style="text-align:center;">Etiqueta</th>
                            <th style="text-align:center;">Grupo</th>
                            <th style="text-align:center;">Agrupamiento</th>
                            <th style="text-align:center;">Cantidad</th>
                            <th style="text-align:center;">Padron</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Estilo personalizado -->
<style>
    #tabla-cruce th,
    #tabla-cruce td {
        font-size: 0.85rem !important;
        vertical-align: middle;
    }

    #tabla-cruce th {
        text-align: center;
    }

    div.dataTables_filter {
        text-align: right !important;
    }
</style>

<?php require_once "includes/footer.php"; ?>

<script>
$(document).ready(function() {
    $('#tabla-cruce').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "modules/cruce_socios/cruce_grupo.php",
            type: "POST",
            error: function(xhr, error, thrown) {
                console.error("Error en AJAX DataTables:", error);
                alert("Error al cargar datos. Revisa la consola para más detalles.");
            }
        },
        columns: [
            { data: "num" },
            { data: "etiqueta_grupo" },
            { data: "nombre_grupo" },
            { data: "nom_agrupamiento" },
            { data: "cantidad_socios_cruzados" },
            { data: "padron", orderable: false, searchable: false }
        ],
        responsive: true,
        autoWidth: false,
        dom:
    "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
    "<'row'<'col-12'tr>>" +
    "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
    });
});
</script>
