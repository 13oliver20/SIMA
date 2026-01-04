<!-- Modal de Registro de Acta de Constitución -->
<div class="modal fade" id="modalRegistrarActa" tabindex="-1" role="dialog" aria-labelledby="modalRegistrarActaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistrarActaLabel">
                    <i class="fas fa-file-alt"></i> Registrar Acta de Constitución
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formRegistrarConstitucion" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="fecha_fundacion">Fecha de Fundación:</label>
                        <input type="date" name="fecha_fundacion" id="fecha_fundacion" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="archivo_acta">Archivo del Acta (PDF):</label>
                        <input type="file" name="archivo_acta" id="archivo_acta" class="form-control" accept=".pdf" required>
                        <small class="form-text text-muted">Solo archivos en formato PDF.</small>
                    </div>

                    <div class="form-group">
                        <label for="grupo_id">Grupo:</label>
                        <div class="d-flex">
                            <select name="grupo_id" id="grupo_id" class="form-control" required>
                                <option value="">Selecciona un grupo</option>
                                <?php
                                require_once(__DIR__ . "/../includes/conexion.php");
                                $query = "SELECT idgrupo, etiqueta_grupo, nombre_grupo FROM grupo";
                                $result = $conn->query($query);
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['idgrupo']}'>{$row['etiqueta_grupo']} - {$row['nombre_grupo']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

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

<!-- Script AJAX con SweetAlert2 -->
<script>
$(document).ready(function() {
    $("#formRegistrarConstitucion").submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Deseas registrar el Acta de Constitución?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Por favor, espera un momento.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: "modules/acta_constitucion/procesar_constitucion.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function(response) {
                        Swal.close();
                        if (response.status === "success") {
                            Swal.fire('¡Éxito!', response.message, 'success');
                            $("#formRegistrarConstitucion")[0].reset();
                            $('#modalRegistrarActa').modal('hide');
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire('Error', 'Hubo un problema al procesar la solicitud.', 'error');
                    }
                });
            }
        });
    });
});
</script>
