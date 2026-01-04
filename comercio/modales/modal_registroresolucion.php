<?php
require_once(__DIR__ . "/../includes/conexion.php");

if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}
?>

<!-- Modal Bootstrap 4 -->
<div class="modal fade" id="modalResolucion" tabindex="-1" role="dialog" aria-labelledby="modalResolucionLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalResolucionLabel">
                    <i class="fas fa-file-upload"></i> Registrar Resolución GDH
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="formRegistrarResolucion" enctype="multipart/form-data">

                    <div class="form-group">
                        <label for="num_resolucion">Número de Resolución:</label>
                        <input type="text" name="num_resolucion" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="fecha_emision">Fecha de Emisión:</label>
                        <input type="date" name="fecha_emision" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="archivo_gdh">Archivo de Resolución GDH (PDF):</label>
                        <input type="file" name="archivo_gdh" id="archivo_gdh" class="form-control" accept=".pdf" required>
                        <small class="form-text text-muted">Solo archivos en formato PDF. Tamaño máximo: 2MB.</small>
                    </div>

                    <div class="form-group">
                        <label for="grupo_id">Grupo:</label>
                        <div class="d-flex">
                            <select name="grupo_id" class="form-control w-100" required>
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
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-success mr-2">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

<!-- Script actualizado con Bootstrap 5 -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("formRegistrarResolucion");

        form.addEventListener("submit", function(e) {
            e.preventDefault();

            const archivo = document.getElementById("archivo_gdh").files[0];

            if (archivo && archivo.size > 2097152) {
                Swal.fire({
                    title: 'Archivo demasiado grande',
                    text: 'El archivo no debe superar los 2MB.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }

            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Deseas registrar esta Resolución GDH?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Por favor, espera un momento.',
                        icon: 'info',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData(form);

                    fetch("modules/resolucion_gdh/procesar_resolucion.php", {
                            method: "POST",
                            body: formData
                        })
                        .then(response => response.json())
                        .then(json => {
                            Swal.close();

                            if (json.status === "success") {
                                Swal.fire({
                                    title: '¡Éxito!',
                                    text: json.message,
                                    icon: 'success',
                                    confirmButtonText: 'Aceptar'
                                }).then(() => {
                                    form.reset();
                                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalResolucion'));
                                    modal.hide();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: json.message,
                                    icon: 'error',
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        })
                        .catch(() => {
                            Swal.close();
                            Swal.fire({
                                title: 'Error',
                                text: 'El grupo ya tiene asignado un número de resolución y archivo.',
                                icon: 'error',
                                confirmButtonText: 'Aceptar'
                            });
                        });
                }
            });
        });
    });
</script>