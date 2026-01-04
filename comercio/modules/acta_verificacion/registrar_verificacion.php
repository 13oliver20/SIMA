<?php
require_once(__DIR__ . "/../../includes/conexion.php");
// Verificar conexión
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-file-alt"></i> Registrar Verificación</h4>
                </div>
                <div class="card-body">
                    <!-- Mensajes de respuesta AJAX -->
                    <div id="mensaje" class="alert d-none"></div>

                    <form id="registrarVerificacionForm" enctype="multipart/form-data">
                        <!-- Selección de Acta de Verificación -->
                        <div class="form-group">
                            <label for="acta_id">Acta de Verificación:</label>
                            <select name="acta_id" class="form-control" required>
                                <option value="">Selecciona un acta</option>
                                <?php
                                $query = "SELECT idacta_verificacion, num_verificacion FROM acta_verificacion";
                                $result = $conn->query($query);
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['idacta_verificacion']}'>{$row['num_verificacion']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Selección de Grupo -->
                        <div class="form-group">
                            <label for="grupo_id">Grupo:</label>
                            <select name="grupo_id" class="form-control" required>
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
                        </div>

                        <!-- Fecha de Verificación -->
                        <div class="form-group">
                            <label for="fecha_verificacion">Fecha de Verificación:</label>
                            <input type="date" name="fecha_verificacion" class="form-control" required>
                        </div>

                        <!-- Archivo de Verificación -->
                        <div class="form-group">
                            <label for="archivo_verificacion">Archivo de Verificación (PDF):</label>
                            <input type="file" name="archivo_verificacion" class="form-control-file" accept=".pdf">
                            <small class="text-muted">Opcional. Solo archivos en formato PDF.</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success" id="guardarBtn">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <a href="?pagina=acta_verificacion/listar_verificacion" class="btn btn-primary">
                                <i class="fas fa-list"></i> Ver Lista
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $("#registrarVerificacionForm").submit(function(e) {
            e.preventDefault(); // Evita la recarga de la página

            // Preguntar al usuario si desea proceder con el registro
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¿Deseas registrar esta verificación?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(this);

                    $.ajax({
                        url: "modules/acta_verificacion/procesar_verificacion.php", // Cambiar al URL de procesamiento
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            Swal.fire({
                               title: 'Procesando...',
                               text: 'Por favor, espera un momento.',
                               icon: 'info',
                               allowOutsideClick: false,
                               didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        },
                        success: function(response) {
                            Swal.close(); // Cerrar alerta de carga

                            if (response.status === "success") {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Éxito',
                                    text: response.message,
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'Aceptar'
                                });

                                $("#registrarVerificacionForm")[0].reset(); // Limpiar el formulario
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                    confirmButtonColor: '#d33',
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Hubo un problema con la solicitud. Por favor, inténtalo de nuevo.',
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    });
                }
            });
        });

        // Validación de archivo (solo PDF)
        $("input[name='archivo_verificacion']").on("change", function() {
            var file = this.files[0];
            if (file && file.type !== "application/pdf") {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Solo se permiten archivos PDF.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar'
                });
                $(this).val(""); // Limpiar input file
            }
        });
    });
</script>
