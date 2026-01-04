<?php require_once(__DIR__ . "/../includes/conexion.php"); ?>

<!-- Modal Bootstrap 4 -->
<div class="modal fade" id="modalAsignarDias" tabindex="-1" role="dialog" aria-labelledby="modalAsignarDiasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalAsignarDiasLabel"><i class="fas fa-calendar"></i> Asignar Días Laborables a un Grupo</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="registrarLaborableForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    
                    <!-- Grupo -->
                    <div class="form-group">
                        <label for="grupo_idgrupo"><strong>Grupo:</strong></label>
                        <select id="grupo_idgrupo" name="grupo_idgrupo" class="form-control" required>
                            <option value="">Selecciona un grupo</option>
                            <?php
                            $query = "SELECT idgrupo, etiqueta_grupo, nombre_grupo FROM grupo WHERE idgrupo NOT IN (SELECT grupo_idgrupo FROM dia_laborable_has_grupo)";
                            $result = $conn->query($query);
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['idgrupo']}'>{$row['etiqueta_grupo']} - {$row['nombre_grupo']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Días Laborables -->
                    <div class="form-group">
                        <label><strong>Días Laborables:</strong></label>
                        <div class="d-flex flex-wrap">
                            <?php
                            $dias = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
                            foreach ($dias as $dia) {
                                echo "<div class='form-check mr-4 mb-2'>
                                        <input class='form-check-input' type='checkbox' name='dias_laborables[]' value='$dia' id='$dia'>
                                        <label class='form-check-label' for='$dia'>$dia</label>
                                    </div>";
                            }
                            ?>
                        </div>
                    </div>

                </div>
               <div class="modal-footer">
          <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
            </form>
        </div>
    </div>
</div>

<!-- Script AJAX con SweetAlert2 -->
<script>
$(document).ready(function () {
    $("#registrarLaborableForm").submit(function (e) {
        e.preventDefault();

        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Deseas registrar los días laborables para el grupo?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, registrar'
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData($("#registrarLaborableForm")[0]);

                $.ajax({
                    url: "modules/laborable/procesar_laborable.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                confirmButtonColor: '#3085d6'
                            });
                            $("select[name='grupo_idgrupo'] option[value='" + response.grupo_idgrupo + "']").remove();
                            $("#registrarLaborableForm")[0].reset();
                            $('#modalAsignarDias').modal('hide');
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
