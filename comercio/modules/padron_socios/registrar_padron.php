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
                    <h4><i class="fas fa-file-upload"></i> Registrar Padrón de Socios</h4>
                </div>
                <div class="card-body">
                    <!-- Mensajes de respuesta AJAX -->
                    <div id="mensaje" class="alert d-none"></div>

                    <form id="formRegistrarPadron" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="archivo_padron">Archivo Padrón de Socios (PDF):</label>
                            <input type="file" name="archivo_padron" id="archivo_padron" class="form-control-file"
                            accept=".pdf" required>
                            <small class="text-muted">Solo archivos en formato PDF.</small>
                        </div>

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

                                <a href="http://localhost/asociaciones/modules/asociaciones/registrar_grupo.php"
                                class="btn btn-primary ml-2">
                                <i class="fas fa-plus"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Botones de Guardar y Ver Lista -->
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                        <a href="?pagina=padron_socios/listar_padron" class="btn btn-primary">
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
        $("#formRegistrarPadron").submit(function(e) {
            e.preventDefault(); // Evita la recarga de la página

            // Preguntar al usuario si desea proceder con el registro
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¿Deseas registrar este padrón de socios?",
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
                        url: "modules/padron_socios/procesar_padron.php",
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

                                $("#formRegistrarPadron")[0].reset();
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
        $("#archivo_padron").on("change", function() {
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
