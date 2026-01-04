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
                    <h4><i class="fas fa-file-upload"></i> Registrar Vigencia de Poder</h4>
                </div>
                <div class="card-body">
                    <!-- Mensajes de respuesta AJAX -->
                    <div id="mensaje" class="alert d-none"></div>

                    <form id="formRegistrarVigencia" enctype="multipart/form-data">
                        <!-- Partida Registral -->
                        <div class="form-group">
                            <label for="partida_registral">Partida Registral:</label>
                            <input type="text" name="partida_registral" id="partida_registral" 
                            maxlength="8" pattern="\d{8}" title="Debe ser un número de 8 dígitos"
                            class="form-control w-100" required>
                        </div>

                        <!-- Archivo de Vigencia -->
                        <div class="form-group">
                            <label for="archivo_vigencia">Archivo de Vigencia (PDF):</label>
                            <input type="file" name="archivo_vigencia" id="archivo_vigencia" 
                            class="form-control-file" accept=".pdf" required>
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
                        <a href="?pagina=vigencia_poder/listar_vigencia" class="btn btn-primary">
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
        $("#formRegistrarVigencia").submit(function(e) {
            e.preventDefault();

            // Alerta de confirmación antes de proceder
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
                    var formData = new FormData(this);

                    // Mostrar SweetAlert2 de cargando
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
                        url: "modules/vigencia_poder/procesar_vigencia.php",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            console.log(response);

                            Swal.close(); // Cerrar alerta de carga

                            let json = typeof response === "string" ? JSON.parse(response) : response;

                            if (json.status === "success") {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Éxito',
                                    text: json.message,
                                    timer: 2500,
                                    showConfirmButton: false
                                });

                                // Reiniciar el formulario
                                $("#formRegistrarVigencia")[0].reset();
                                $("#grupo_id").val("").trigger("change");
                                $("#archivo_vigencia").val("");
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: json.message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error en la solicitud: ' + error
                            });
                        }
                    });
                }
            });
        });
    });
</script>
