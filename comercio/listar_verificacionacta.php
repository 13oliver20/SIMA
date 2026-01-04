<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid mt-4">
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2 class="mb-2 mb-md-0"><i class="fas fa-file-alt"></i> Listado de Actas de Verificación</h2>
                <!-- Si quieres botón para agregar, déjalo aquí -->
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalRegistrarVerificacion">
                    <i class="fas fa-plus"></i> Nueva Acta
                </button>
            </div>

            <div class="table-responsive">
                <table id="tabla_grupos" class="table table-bordered table-sm table-hover table-striped align-middle" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>Nº</th> <!-- Agregado contador -->
                            <th>Nombre Grupo</th>
                            <th>Primera Verificación</th>
                            <th>Fecha</th>
                            <th>Archivo</th>
                            <th>Segunda Verificación</th>
                            <th>Fecha</th>
                            <th>Archivo</th>
                            <th>Tercera Verificación</th>
                            <th>Fecha</th>
                            <th>Archivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Estilo personalizado para texto más pequeño en la tabla -->
<style>
    #tabla_grupos th,
    #tabla_grupos td {
        font-size: 0.75rem !important;
        vertical-align: middle;
    }

    div.dataTables_filter {
        text-align: right !important;
    }
</style>

<?php require_once "includes/footer.php"; ?>
<?php require_once "modales/modal_actaverificacion.php"; ?>

<script>
    $(document).ready(function() {
        $('#tabla_grupos').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "modules/acta_verificacion/cargar_verificacion.php",
                type: "POST"
            },
            columns: [
                { data: "contador" },
                { data: "nombre_grupo" },
                { data: "verificacion_1" },
                { data: "fecha_1" },
                { data: "archivo_1" },
                { data: "verificacion_2" },
                { data: "fecha_2" },
                { data: "archivo_2" },
                { data: "verificacion_3" },
                { data: "fecha_3" },
                { data: "archivo_3" },
                { data: "acciones" }
            ],language: {
        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
    },
            responsive: true,
            autoWidth: false,
            dom:
                "<'row mb-3'<'col-sm-6'l><'col-sm-6 text-right'f>>" +  // quitamos la 'B' para botones
                "<'row'<'col-12'tr>>" +
                "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>"
        });
    });
</script>

