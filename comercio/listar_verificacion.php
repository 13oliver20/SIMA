<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Tabla de verificación de socios -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <!-- Título alineado -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2 class="mb-2 mb-md-0"><i class="fas fa-user-check"></i> Listado de Verificación de Socios</h2>
                <!-- Si necesitas botón nuevo, puedes agregar aquí -->
                <!-- <button class="btn btn-primary" data-toggle="modal" data-target="#modalRegistrarVerificacion">
                    <i class="fas fa-plus"></i> Nueva Verificación
                </button> -->
            </div>

            <div class="table-responsive">
                <table id="tabla_verificacion" class="display" width="100%">
                    <thead>
                        <tr>
                            <th>#</th> <!-- Contador -->
                            <th>DNI</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Grupo</th>
                            <th>Primera Verificación</th>
                            <th>Fecha</th>
                            <th>Segunda Verificación</th>
                            <th>Fecha</th>
                            <th>Tercera Verificación</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Estilo personalizado para texto más pequeño en la tabla -->
<style>
    #tabla_verificacion th,
    #tabla_verificacion td {
        font-size: 0.75rem !important; /* Ajusta el tamaño según necesidad */
        vertical-align: middle;
    }

    div.dataTables_filter {
        text-align: right !important;
    }
</style>

<?php require_once "includes/footer.php"; ?>

    <script>
        $(document).ready(function() {
            $('#tabla_verificacion').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "modules/verificacion/cargar_verificacion.php",
                    "type": "POST"
                },
                "columns": [
                    { "data": "contador" }, // Muestra el contador en la primera columna
                    { "data": "dni" },
                    { "data": "nombre" },
                    { "data": "apellidos" },
                    { "data": "grupo" },
                    { "data": "primera_verificacion" },
                    { "data": "primera_fecha" },
                    { "data": "segunda_verificacion" },
                    { "data": "segunda_fecha" },
                    { "data": "tercera_verificacion" },
                    { "data": "tercera_fecha" },
                    { "data": "estado" }
                ],
                language: {
        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
    },
                responsive: true,
        autoWidth: false,
        dom:
    "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
    "<'row'<'col-12'tr>>" +
    "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
                "buttons": [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                        title: 'Listado_Verificación_Socios',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> Exportar a PDF',
                        title: 'Listado_Verificación_Socios',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        className: 'btn btn-danger'
                    }
                ]
            });
        });
    </script>

