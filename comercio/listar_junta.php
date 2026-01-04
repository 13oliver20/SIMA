<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Tabla de junta directiva -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <!-- Título y Botón alineados -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2 class="mb-2 mb-md-0"><i class="fas fa-users-cog"></i> Listado de Junta Directiva</h2>
                <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#modalRegistrarJunta">
                    <i class="fas fa-file-alt"></i> Registrar Junta
                </button>
            </div>

            <div class="table-responsive">
                <table id="tabla-junta-directiva" class="display" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center">Nº</th>
                            <th class="text-center">DNI</th>
                            <th class="text-center">Nombre Completo</th>
                            <th class="text-center">Grupo</th>
                            <th class="text-center">Cargo</th>
                            <th class="text-center">Fecha - Inicio</th>
                            <th class="text-center">Fecha - Fin</th>
                            <th class="text-center">Celular</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Estilo personalizado para texto más pequeño en la tabla -->
<style>
    #tabla-junta-directiva th,
    #tabla-junta-directiva td {
        font-size: 0.75rem !important;
        vertical-align: middle;
    }

    div.dataTables_filter {
        text-align: right !important;
    }
</style>

<!-- Incluir modal -->
<?php include 'modales/modal_registrojunta.php'; ?>
<?php require_once "includes/footer.php"; ?>

<script>
$(document).ready(function() {
    var tablaJunta = $('#tabla-junta-directiva').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "modules/junta_directiva/cargar_junta.php",
            type: "POST"
        },
        columns: [
            { data: "num" },
            { data: "dni_socio" },
            { data: "nombre_socio" },
            { data: "nombre_grupo" },
            { data: "nombre_cargo" },
            { data: "fecha_inicio" },
            { data: "fecha_fin" },
            { data: "celular" },
            { data: "acciones", orderable: false, searchable: false }
        ],
        language: {
        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
    },
        responsive: true,
        autoWidth: false,
        dom:
            "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
            "<'row'<'col-12'tr>>" +
            "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>" +
            "Brtip",
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-success',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> Exportar a PDF',
                className: 'btn btn-danger',
                orientation: 'portrait',
                pageSize: 'A4',
                title: 'Listado de Junta Directiva',
                exportOptions: { columns: ':not(:last-child)' }
            }
        ]
    });

    window.confirmarEliminar = function(id) {
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
            fetch('modules/junta_directiva/eliminar_junta.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ idjunta_directiva: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    Swal.fire("¡Eliminado!", data.message, "success");
                    tablaJunta.ajax.reload(null, false);
                } else {
                    Swal.fire("Error", data.message, "error");
                }
            })
            .catch(() => {
                Swal.fire("Error", "Hubo un problema al conectar con el servidor.", "error");
            });
        }
    });
}

});
</script>
