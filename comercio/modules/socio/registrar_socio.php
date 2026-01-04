<?php
require_once(__DIR__ . "/../../includes/conexion.php");
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}
?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-user-plus"></i> Registrar Nuevo Socio</h4>
                </div>
                <div class="card-body">
                    <form id="formRegistrarSocio" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dni">DNI:</label>
                                    <input type="text" name="dni" maxlength="8" pattern="\d{8}" title="Debe ser un número de 8 dígitos" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">Nombres:</label>
                                    <input type="text" name="nombre" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="apellido_pat">Apellido Paterno:</label>
                                    <input type="text" name="apellido_pat" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="apellido_mat">Apellido Materno:</label>
                                    <input type="text" name="apellido_mat" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="genero">Género:</label>
                                    <select name="genero" class="form-control" required>
                                        <option value="">- Seleccionar Género -</option>
                                        <option value="F">Femenino</option>
                                        <option value="M">Masculino</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="departamento">Departamento:</label>
                                    <input type="text" name="departamento" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="provincia">Provincia:</label>
                                    <input type="text" name="provincia" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="distrito">Distrito:</label>
                                    <input type="text" name="distrito" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <a href="?pagina=socio/listar_socio" class="btn btn-primary">
                                <i class="fas fa-list"></i> Ver Lista
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AJAX con confirmación y alertas -->
<script>
$(document).ready(function () {
    $("#formRegistrarSocio").submit(function (event) {
        event.preventDefault(); // Evita la recarga

        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Deseas registrar este socio?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, registrar'
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = $(this).serialize();

                $.ajax({
                    type: "POST",
                    url: "modules/socio/procesar_socio.php",
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Registrado!',
                                text: response.message,
                                confirmButtonColor: '#3085d6'
                            });
                            $("#formRegistrarSocio")[0].reset();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: 'No se pudo conectar con el servidor.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    });
});
</script>
