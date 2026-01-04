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
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-file-upload"></i> Registrar Resolución GDH</h4>
                </div>
                <div class="card-body">
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
                            <input type="file" name="archivo_gdh" id="archivo_gdh" class="form-control-file" accept=".pdf" required>
                            <small class="text-muted">Solo archivos en formato PDF. Tamaño máximo: 2MB.</small>
                        </div>

                        <div class="form-group">
                            <label for="grupo">Grupo:</label>
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

                                <a href="../../modules/asociaciones/registrar_grupo.php" class="btn btn-primary ml-2">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <a href="?pagina=resolucion_gdh/listar_gdh" class="btn btn-primary">
                                <i class="fas fa-list"></i> Ver Lista
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Librerías necesarias -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Script AJAX con SweetAlert2 -->
<script>
    $(document).ready(function () {
        $("#formRegistrarResolucion").submit(function (e) {
            e.preventDefault(); // Evita el envío tradicional del formulario

            var formData = new FormData(this);
            var archivo = $("#archivo_gdh")[0].files[0];

            // Validación del tamaño del archivo (máx 2MB)
            if (archivo && archivo.size > 2097152) {
                Swal.fire({
                    title: 'Archivo demasiado grande',
                    text: 'El archivo no debe superar los 2MB.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }

            // Confirmación
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

                    $.ajax({
                        url: "modules/resolucion_gdh/procesar_resolucion.php",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        timeout: 10000,
                        success: function (response) {
                            Swal.close();

                            try {
                                var json = typeof response === "string" ? JSON.parse(response) : response;

                                if (json.status === "success") {
                                    Swal.fire({
                                        title: '¡Éxito!',
                                        text: json.message,
                                        icon: 'success',
                                        confirmButtonText: 'Aceptar'
                                    }).then((r) => {
                                        if (r.isConfirmed) {
                                            $("#formRegistrarResolucion")[0].reset();
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: json.message,
                                        icon: 'error',
                                        confirmButtonText: 'Aceptar'
                                    });
                                }
                            } catch (e) {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'El grupo ya tiene asignado un número de resolución y archivo.',
                                    icon: 'error',
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        },
                        error: function () {
                            Swal.close();
                            Swal.fire({
                                title: 'Error',
                                text: 'Error en la solicitud.',
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
