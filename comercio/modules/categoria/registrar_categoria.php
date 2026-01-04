<?php
require_once(__DIR__ . "/../../includes/conexion.php");

if (!$conn) {
    echo "<div class='alert alert-danger'>Error: No se pudo conectar a la base de datos.</div>";
    exit;
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow w-100">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-plus"></i> Registrar Nueva Categoría</h4>
                </div>
                <div class="card-body">

                    <!-- Contenedor de alertas (comentado porque usamos SweetAlert2) -->
                    <!-- <div id="mensaje" class="d-none"></div> -->

                    <form id="registrarCategoriaForm" method="POST">
                        <div class="form-group">
                            <label for="tipo">Tipo de Categoría:</label>
                            <input type="text" id="tipo" name="tipo" class="form-control" required>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <a href="?pagina=categoria/listar_categoria" class="btn btn-primary">
                                <i class="fas fa-list"></i> Ver Lista
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts necesarios -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $("#registrarCategoriaForm").submit(function(event) {
        event.preventDefault(); // Evitar envío inmediato

        // Mostrar confirmación antes de enviar
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Deseas registrar esta categoría?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                enviarFormulario(); // Si confirma, se envía
            }
        });
    });

    function enviarFormulario() {
        let formData = $("#registrarCategoriaForm").serialize();

        $.ajax({
            url: "modules/categoria/procesar_categoria.php",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                mostrarAlerta(response.status, response.message);

                if (response.status === "success") {
                    $("#registrarCategoriaForm")[0].reset();
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
