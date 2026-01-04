<?php
require_once(__DIR__ . "/../../includes/conexion.php");

if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow w-100">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-file-alt"></i> Registrar Acta de Constitución</h4>
                </div>
                <div class="card-body">
                    <!-- Mensajes de respuesta AJAX -->
                    <div id="mensaje" class="alert d-none"></div>

                    <form id="formRegistrarConstitucion" enctype="multipart/form-data">
                        <!-- Fecha de Fundación -->
                        <div class="form-group">
                            <label for="fecha_fundacion">Fecha de Fundación:</label>
                            <input type="date" name="fecha_fundacion" id="fecha_fundacion" class="form-control w-100" required>
                        </div>

                        <!-- Archivo del Acta -->
                        <div class="form-group">
                            <label for="archivo_acta">Archivo del Acta (PDF):</label>
                            <input type="file" name="archivo_acta" id="archivo_acta" class="form-control-file" accept=".pdf" required>
                            <small class="text-muted">Solo archivos en formato PDF.</small>
                        </div>

                        <!-- Grupo -->
                        <div class="form-group">
                            <label for="grupo">Grupo:</label>
                            <div class="d-flex">
                                <select name="grupo_id" id="grupo_id" class="form-control w-100" required>
                                    <option value="">Selecciona un grupo</option>
                                    <?php
                                    $query = "SELECT idgrupo, etiqueta_grupo, nombre_grupo FROM grupo";
                                    $result = $conn->query($query);
                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $idgrupo = $row['idgrupo'];
                                            $etiqueta = htmlspecialchars($row['etiqueta_grupo']);
                                            $nombre = htmlspecialchars($row['nombre_grupo']);
                                            echo "<option value='{$idgrupo}'>{$etiqueta} - {$nombre}</option>";
                                        }
                                    } else {
                                        echo "<option disabled>No hay grupos disponibles</option>";
                                    }
                                    ?>
                                </select>

                                <a href="http://localhost/asociaciones/modules/asociaciones/registrar_grupo.php" class="btn btn-primary ml-2">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Botones de Guardar y Ver Lista -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <a href="?pagina=acta_constitucion/listar_constitucion" class="btn btn-primary">
                                <i class="fas fa-list"></i> Ver Lista
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script AJAX con SweetAlert2 -->
<script>
    $(document).ready(function() {
        $("#formRegistrarConstitucion").submit(function(e) {
            e.preventDefault(); // Evita el envío tradicional

            var formData = new FormData(this);

            // Mostrar una alerta de confirmación antes de continuar
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Deseas registrar el Acta de Constitución?',
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Sí, registrar',
                reverseButtons: true  // Cambia el orden de los botones
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar la alerta de "Procesando"
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Por favor, espera un momento.',
                        icon: 'info',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Realizar la solicitud AJAX
                    $.ajax({
                        url: "modules/acta_constitucion/procesar_constitucion.php",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            // Cerrar la alerta de "Procesando"
                            Swal.close();

                            if (response.status === "success") {
                                // Mostrar alerta de éxito
                                Swal.fire({
                                    title: '¡Éxito!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'Aceptar'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Resetear el formulario después de la confirmación
                                        $("#formRegistrarConstitucion")[0].reset();
                                    }
                                });
                            } else {
                                // Mostrar alerta de error
                                Swal.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        },
                        error: function() {
                            // Cerrar la alerta de "Procesando"
                            Swal.close();

                            // Mostrar alerta de error en caso de fallo de la solicitud
                            Swal.fire({
                                title: 'Error',
                                text: 'Hubo un problema al procesar la solicitud.',
                                icon: 'error',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
