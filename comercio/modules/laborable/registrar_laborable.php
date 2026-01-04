<?php
require_once(__DIR__ . "/../../includes/conexion.php");
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-calendar"></i> Asignar Días Laborables a un Grupo</h4>
                </div>
                <div class="card-body">
                    <form id="registrarLaborableForm" method="POST" enctype="multipart/form-data">
                        
                        <!-- Grupo -->
                        <div class="form-group">
                            <label for="grupo_idgrupo">Grupo:</label>
                            <select id="grupo_idgrupo" name="grupo_idgrupo" class="form-control" required>
                                <option value="">Selecciona un grupo</option>
                                <?php
                                // Obtener los grupos que no tienen días laborables asignados
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
                            <label>Días Laborables:</label>
                            <div class="d-flex flex-wrap">
                                <?php
                                $dias = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
                                foreach ($dias as $dia) {
                                    echo "<div class='form-check mr-3'>
                                            <input class='form-check-input' type='checkbox' name='dias_laborables[]' value='$dia' id='$dia'>
                                            <label class='form-check-label' for='$dia'>$dia</label>
                                        </div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <a href="?pagina=laborable/listar_laborable" class="btn btn-primary">
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
$(document).ready(function () {
    $("#registrarLaborableForm").submit(function (e) {
        e.preventDefault(); // Evita la recarga

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

                            // Aquí quitamos el grupo registrado de la lista
                            $("select[name='grupo_idgrupo'] option[value='" + response.grupo_idgrupo + "']").remove();
                            $("#registrarLaborableForm")[0].reset();
                            $('#grupo_idgrupo').val(null).trigger('change');  // Resetear el campo de selección
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

