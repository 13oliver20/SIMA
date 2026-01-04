<!-- Botón para abrir el modal (opcional, ejemplo) -->
<!--
<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalEditarSocio">
  Editar Socio
</button>
-->

<!-- Modal Editar Socio -->
<div class="modal fade" id="modalEditarSocio" tabindex="-1" role="dialog" aria-labelledby="modalEditarSocioLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarSocioLabel">Editar Socio</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="formEditarSocio">
          <input type="hidden" name="idsocio" id="edit-idsocio">

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="edit-dni">DNI</label>
              <input type="text" class="form-control" id="edit-dni" name="dni" required>
            </div>
            <div class="form-group col-md-6">
              <label for="edit-nombre">Nombre</label>
              <input type="text" class="form-control" id="edit-nombre" name="nombre" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="edit-apellido_pat">Apellido Paterno</label>
              <input type="text" class="form-control" id="edit-apellido_pat" name="apellido_pat" required>
            </div>
            <div class="form-group col-md-4">
              <label for="edit-apellido_mat">Apellido Materno</label>
              <input type="text" class="form-control" id="edit-apellido_mat" name="apellido_mat">
            </div>
            <div class="form-group col-md-4">
              <label for="edit-genero">Género</label>
              <select class="form-control" id="edit-genero" name="genero" required>
                <option value="">Seleccione</option>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="edit-departamento">Departamento</label>
              <input type="text" class="form-control" id="edit-departamento" name="departamento" required>
            </div>
            <div class="form-group col-md-4">
              <label for="edit-provincia">Provincia</label>
              <input type="text" class="form-control" id="edit-provincia" name="provincia" required>
            </div>
            <div class="form-group col-md-4">
              <label for="edit-distrito">Distrito</label>
              <input type="text" class="form-control" id="edit-distrito" name="distrito">
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="submit" form="formEditarSocio" class="btn btn-success">
          <i class="fas fa-save"></i> Guardar Cambios
        </button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times"></i> Cancelar
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  .modal-content {
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }
</style>

<script>
  $('#formEditarSocio').on('submit', function (e) {
    e.preventDefault();
    const formData = $(this).serialize();

    Swal.fire({
      title: '¿Estás seguro?',
      text: '¿Deseas actualizar este socio?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, actualizar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#d33'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: 'modules/socio/editar_socio.php',
          type: 'POST',
          data: formData,
          dataType: 'json',
          success: function (response) {
            if (response.success) {
              Swal.fire('Actualizado', response.message, 'success');
              $('#modalEditarSocio').modal('hide');
              $('#tabla-socios').DataTable().ajax.reload(null, false);
            } else {
              Swal.fire('Error', response.message, 'error');
            }
          },
          error: function (xhr) {
            console.log("Respuesta del servidor:", xhr.responseText);
            Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
          }
        });
      }
    });
  });
</script>
