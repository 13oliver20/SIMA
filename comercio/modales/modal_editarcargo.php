<!-- Modal para editar cargo -->
<div class="modal fade" id="modalEditarCargo" tabindex="-1" role="dialog" aria-labelledby="modalEditarLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formEditarCargo">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Cargo</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="edit_idcargo" name="idcargo">
          <div class="form-group">
            <label for="edit_tipo_cargo">Tipo de Cargo</label>
            <input type="text" class="form-control" id="edit_tipo_cargo" name="tipo_cargo" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Guardar Cambios</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
// Enviar formulario de edición
  $('#formEditarCargo').submit(function(e) {
    e.preventDefault();

    $.ajax({
      url: 'modules/cargo/editar_cargo.php',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          $('#modalEditarCargo').modal('hide');
          $('#tablaCargos').DataTable().ajax.reload(null, false);

          Swal.fire('¡Actualizado!', 'El cargo se actualizó correctamente.', 'success');
        } else {
          Swal.fire('Error', response.error || 'No se pudo actualizar el cargo', 'error');
        }
      },
      error: function() {
        Swal.fire('Error', 'Error en la comunicación con el servidor', 'error');
      }
    });
  });
</script>