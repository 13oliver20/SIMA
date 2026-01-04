<?php
require_once(__DIR__ . "/../includes/conexion.php");
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}
?>

<!-- Modal Registrar Grupo -->
<div class="modal fade" id="modalRegistrarGrupo" tabindex="-1" role="dialog" aria-labelledby="modalRegistrarGrupoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistrarGrupoLabel"><i class="fas fa-users"></i> Registrar Grupo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulario para registrar grupo -->
                <form id="formGrupo">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="etiqueta_grupo">Código Etiqueta:</label>
                                <input type="text" id="etiqueta_grupo" name="etiqueta_grupo" class="form-control" maxlength="7" style="max-width: 120px;" required>
                            </div>
                        </div>

                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="nombre_grupo">Nombre de la Asociación:</label>
                                <input type="text" id="nombre_grupo" name="nombre_grupo" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ubicacion">Ubicación:</label>
                        <input type="text" id="ubicacion" name="ubicacion" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="agrupamiento_id">Agrupamiento:</label>
                                <select id="agrupamiento_id" name="agrupamiento_id" class="form-control" required>
                                    <option value="">Selecciona un agrupamiento</option>
                                    <?php
                                    $query = "SELECT idagrupamiento, nom_agrupamiento FROM agrupamiento";
                                    if ($stmt = $conn->prepare($query)) {
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='{$row['idagrupamiento']}'>{$row['nom_agrupamiento']}</option>";
                                        }
                                        $stmt->close();
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="categoria_id">Categoría:</label>
                                <select id="categoria_id" name="categoria_id" class="form-control" required>
                                    <option value="">Selecciona una categoría</option>
                                    <?php
                                    $query = "SELECT idcategoria, tipo FROM categoria";
                                    if ($stmt = $conn->prepare($query)) {
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='{$row['idcategoria']}'>{$row['tipo']}</option>";
                                        }
                                        $stmt->close();
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="estado">Estado:</label>
                                <select id="estado" name="estado" class="form-control" required>
                                    <option value="">- Seleccionar estado -</option>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                            </div>
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

<!-- Script AJAX para guardar el grupo y actualizar la tabla -->
<script>
$(document).ready(function () {
    $("#formGrupo").submit(function (event) {
        event.preventDefault(); // Prevenir envío inmediato

        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Deseas registrar este grupo?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "modules/asociaciones/procesar_grupo.php",
                    data: $("#formGrupo").serialize(),
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Registrado',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $("#formGrupo")[0].reset(); // Resetear formulario
                            $('#modalRegistrarGrupo').modal('hide'); // Cerrar modal

                            // Recargar DataTable sin perder paginación ni orden
                            $('#tabla-grupos').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Ocurrió un error inesperado.', 'error');
                    }
                });
            }
        });
    });
});
</script>
