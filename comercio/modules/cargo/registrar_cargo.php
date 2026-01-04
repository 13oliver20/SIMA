<?php

// Verificar si los archivos existen antes de requerirlos
require_once(__DIR__ . "/../../includes/conexion.php");

// Verificar conexión
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}
?>
<div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow w-100">
                    <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-user-plus"></i> Registrar Nuevo Cargo</h4>
                </div>
                <div class="card-body">

                    <!-- Contenedor de alertas -->
                    <div id="mensaje" class="d-none"></div>

                    <form id="registrarCargoForm" method="POST">
                        <div class="form-group">
                            <label for="tipo_cargo">Tipo de Cargo:</label>
                            <input type="text" id="tipo_cargo" name="tipo_cargo" class="form-control" required>
                        </div>

                         <!-- Botones de Guardar y Ver Lista -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <a href="?pagina=cargo/listar_cargo" class="btn btn-primary">
                                <i class="fas fa-list"></i> Ver Lista
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#registrarCargoForm").submit(function(event) {
        event.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            url: "modules/cargo/procesar_cargo.php",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                mostrarAlerta(response.status, response.message);

                if (response.status === "success") {
                    $("#registrarCargoForm")[0].reset();
                }
            },
            error: function() {
                mostrarAlerta("danger", "Error al procesar la solicitud.");
            }
        });
    });

    function mostrarAlerta(tipo, mensaje) {
        let alerta = $("#mensaje");

        // Limpiar clases anteriores
        alerta.removeClass("d-none alert-success alert-danger alert-info alert-warning");

        // Añadir la clase correspondiente a la alerta
        alerta.addClass("alert alert-" + tipo);

        // Establecer el mensaje
        alerta.html(mensaje);

        // Mostrar la alerta
        alerta.removeClass("d-none");

        // Desvanecer la alerta después de 3 segundos
        setTimeout(function() {
            alerta.fadeOut(500);
        }, 3000);
    }
});
</script>
