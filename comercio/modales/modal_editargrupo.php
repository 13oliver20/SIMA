<!-- Modal Editar Grupo (Bootstrap 4) -->
<div class="modal fade" id="modalEditarGrupo" tabindex="-1" role="dialog" aria-labelledby="modalEditarGrupoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form id="formEditarGrupo">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditarGrupoLabel"><i class="fas fa-edit"></i> Editar Grupo</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="edit_idgrupo" name="idgrupo">
          <div class="form-row">
            <div class="form-group col-md-3">
              <label for="edit_etiqueta_grupo">Código Etiqueta:</label>
              <input type="text" id="edit_etiqueta_grupo" name="etiqueta_grupo" class="form-control" maxlength="7" readonly>
            </div>

            <div class="form-group col-md-9">
              <label for="edit_nombre_grupo">Nombre de la Asociación:</label>
              <input type="text" id="edit_nombre_grupo" name="nombre_grupo" class="form-control" required>
            </div>
          </div>

          <div class="form-group">
            <label for="edit_ubicacion">Ubicación:</label>
            <input type="text" id="edit_ubicacion" name="ubicacion" class="form-control" required>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="edit_agrupamiento_id">Agrupamiento:</label>
              <select id="edit_agrupamiento_id" name="agrupamiento_id" class="form-control" required>
                <?php
                $query = "SELECT idagrupamiento, nom_agrupamiento FROM agrupamiento";
                $res = $conn->query($query);
                while ($row = $res->fetch_assoc()) {
                  echo "<option value='{$row['idagrupamiento']}'>{$row['nom_agrupamiento']}</option>";
                }
                ?>
              </select>
            </div>

            <div class="form-group col-md-4">
              <label for="edit_categoria_id">Categoría:</label>
              <select id="edit_categoria_id" name="categoria_id" class="form-control" required>
                <?php
                $query = "SELECT idcategoria, tipo FROM categoria";
                $res = $conn->query($query);
                while ($row = $res->fetch_assoc()) {
                  echo "<option value='{$row['idcategoria']}'>{$row['tipo']}</option>";
                }
                ?>
              </select>
            </div>

            <div class="form-group col-md-4">
              <label for="edit_estado">Estado:</label>
              <select id="edit_estado" name="estado" class="form-control" required>
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
              </select>
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

<script>
  $('#formEditarGrupo').on('submit', function(e) {
    e.preventDefault();
    Swal.fire({
      title: '¿Actualizar grupo?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, actualizar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: 'modules/asociaciones/editar_grupo.php',
          type: 'POST',
          data: $('#formEditarGrupo').serialize(),
          dataType: 'json', // Asegúrate de que la respuesta sea JSON
          success: function(response) {
            console.log(response); // Ver la respuesta del servidor en la consola
            if (response.status === 'success') {
              Swal.fire('Actualizado', response.message, 'success');
              $('#modalEditarGrupo').modal('hide');
              $('#tabla-grupos').DataTable().ajax.reload(null, false);
            } else {
              Swal.fire('Error', response.message, 'error');
            }
          },
          error: function(xhr, status, error) {
            console.error("Error en la solicitud:", xhr.responseText); // Log del error
            Swal.fire('Error', 'Error en la solicitud.', 'error');
          }
        });
      }
    });
  });
</script>