<?php
require_once(__DIR__ . "/../../includes/conexion.php");
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}
?>

<!-- Modal -->
<div class="modal fade" id="modalVerificacion" tabindex="-1" aria-labelledby="modalVerificacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalVerificacionLabel"><i class="fas fa-file-alt"></i> Registrar Verificación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="mensaje" class="alert d-none"></div>

                <form id="registrarVerificacionForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="acta_id" class="form-label">Acta de Verificación:</label>
                        <select name="acta_id" class="form-select" required>
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

                    <div class="mb-3">
                        <label for="grupo_id" class="form-label">Grupo:</label>
                        <select name="grupo_id" class="form-select" required>
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

                    <div class="mb-3">
                        <label for="fecha_verificacion" class="form-label">Fecha de Verificación:</label>
                        <input type="date" name="fecha_verificacion" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="archivo_verificacion" class="form-label">Archivo de Verificación (PDF):</label>
                        <input type="file" name="archivo_verificacion" class="form-control" accept=".pdf">
                        <div class="form-text">Opcional. Solo archivos en formato PDF.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="submit" form="registrarVerificacionForm" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <a href="?pagina=acta_verificacion/listar_verificacion" class="btn btn-primary">
                    <i class="fas fa-list"></i> Ver Lista
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Scripts necesarios -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- JS para envío y validación -->
<script>
    $(document).ready(function() {
        $("#registrarVerificacionForm").submit(function(e) {
            e.preventDefault();

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
                    let formData = new FormData(this);

                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Por favor, espera un momento.',
                        icon: 'info',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "modules/acta_verificacion/procesar_verificacion.php",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.close();

                            let json = typeof response === "string" ? JSON.parse(response) : response;

                            if (json.status === "success") {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Éxito',
                                    text: json.message,
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'Aceptar'
                                }).then(() => {
                                    $("#registrarVerificacionForm")[0].reset();
                                    bootstrap.Modal.getInstance(document.getElementById('modalVerificacion')).hide();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: json.message,
                                    confirmButtonColor: '#d33'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Hubo un problema con la solicitud.',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            });
        });

        // Validación archivo PDF
        $("input[name='archivo_verificacion']").on("change", function() {
            const file = this.files[0];
            if (file && file.type !== "application/pdf") {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo no válido',
                    text: 'Solo se permiten archivos PDF.',
                    confirmButtonColor: '#3085d6'
                });
                this.value = "";
            }
        });
    });
</script>
