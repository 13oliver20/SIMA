<?php
require_once(__DIR__ . "/../includes/conexion.php");

if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}
?>

<!-- Modal: Registrar Vigencia de Poder -->
<div class="modal fade" id="modalVigencia" tabindex="-1" role="dialog" aria-labelledby="modalVigenciaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalVigenciaLabel">
                    <i class="fas fa-file-alt"></i> Registrar Vigencia de Poder
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="mensaje" class="alert d-none"></div>

                <form id="formRegistrarVigencia" enctype="multipart/form-data">
                    <!-- Partida Registral -->
                    <div class="form-group">
                        <label for="partida_registral">Partida Registral:</label>
                        <input type="text" name="partida_registral" id="partida_registral"
                            maxlength="8" pattern="\d{8}" title="Debe ser un número de 8 dígitos"
                            class="form-control" required>
                    </div>

                    <!-- Archivo PDF -->
                    <div class="form-group">
                        <label for="archivo_vigencia">Archivo de Vigencia (PDF):</label>
                        <input type="file" name="archivo_vigencia" id="archivo_vigencia"
                            class="form-control" accept=".pdf" required>
                        <small class="form-text text-muted">Solo archivos en formato PDF.</small>
                    </div>

                    <!-- Grupo -->
                    <div class="form-group">
                        <label for="grupo_id">Grupo:</label>
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
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="text-right mt-3">
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

<!-- Script para manejar el envío con SweetAlert y cierre del modal en Bootstrap 4 -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("formRegistrarVigencia");

        form.addEventListener("submit", function (e) {
            e.preventDefault();

            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Deseas registrar la vigencia de poder con los datos ingresados?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(form);

                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Por favor, espera un momento.',
                        icon: 'info',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch("modules/vigencia_poder/procesar_vigencia.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(json => {
                        Swal.close();

                        if (json.status === "success") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: json.message,
                                timer: 2500,
                                showConfirmButton: false
                            });

                            form.reset();
                            $('#modalVigencia').modal('hide');

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: json.message
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error en la solicitud: ' + error
                        });
                    });
                }
            });
        });
    });
</script>
