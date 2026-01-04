<?php
require_once(__DIR__ . "/../../includes/conexion.php");
// Verificar conexión
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-layer-group"></i> Registrar Nuevo Agrupamiento</h4>
                </div>
                <div class="card-body">

                    <!-- Contenedor de alertas (ya no es necesario con SweetAlert2) -->
                    <!-- <div id="mensaje" class="d-none"></div> -->

                    <form id="registrarAgrupamientoForm" method="POST">
                        <div class="form-group row">

                            <div class="col-12 col-md-3">
                                <label for="cod_etiqueta">Código Agrupamiento:</label>
                                <input type="text" name="cod_etiqueta" class="form-control" required>
                            </div>

                            <div class="col-12 col-md-9">
                                <label for="nom_agrupamiento">Nombre del Agrupamiento:</label>
                                <input type="text" name="nom_agrupamiento" class="form-control" required>
                            </div>
                        </div>

                        <!-- Botones de Guardar y Ver Lista -->
                        <div class="d-flex justify-content-between mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <a href="?pagina=agrupamiento/listar_agrupamiento" class="btn btn-primary">
                                <i class="fas fa-list"></i> Ver Lista
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $("#registrarAgrupamientoForm").submit(function(event) {
        event.preventDefault(); // Evita el envío inmediato del formulario

        Swal.fire({
            title: '¿Confirmar registro?',
            text: "¿Deseas guardar este agrupamiento?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                enviarFormulario();
            }
        });
    });

    function enviarFormulario() {
        let formData = $("#registrarAgrupamientoForm").serialize();

        $.ajax({
            url: "modules/agrupamiento/procesar_agrupamiento.php",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                mostrarAlerta(response.status, response.message);

                if (response.status === "success") {
                    $("#registrarAgrupamientoForm")[0].reset();
                }
            },
            error: function() {
                mostrarAlerta("danger", "Error al procesar la solicitud.");
            }
        });
    }

    function mostrarAlerta(tipo, mensaje) {
        let icono = "info";

        if (tipo === "success") icono = "success";
        else if (tipo === "danger") icono = "error";
        else if (tipo === "warning") icono = "warning";

        Swal.fire({
            icon: icono,
            title: mensaje,
            confirmButtonText: 'Aceptar',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false
        });
    }
});
</script>
