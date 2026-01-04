<!-- Modal Registrar Socio -->
<div class="modal fade" id="modalRegistrarSocio" tabindex="-1" aria-labelledby="modalRegistrarSocioLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 class="modal-title" id="modalRegistrarSocioLabel">Registrar Nuevo Socio</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="formRegistrarSocio" method="POST" novalidate>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="dni">DNI</label>
              <input type="text" class="form-control" id="dni" name="dni" maxlength="8" pattern="\d{8}" required>
            </div>
            <div class="form-group col-md-6">
              <label for="nombre">Nombres</label>
              <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="apellido_pat">Apellido Paterno</label>
              <input type="text" class="form-control" id="apellido_pat" name="apellido_pat" required>
            </div>
            <div class="form-group col-md-4">
              <label for="apellido_mat">Apellido Materno</label>
              <input type="text" class="form-control" id="apellido_mat" name="apellido_mat">
            </div>
            <div class="form-group col-md-4">
              <label for="genero">Género</label>
              <select id="genero" name="genero" class="form-control" required>
                <option value="">Seleccione</option>
                <option value="F">Femenino</option>
                <option value="M">Masculino</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="departamento">Departamento</label>
              <input type="text" class="form-control" id="departamento" name="departamento" required>
            </div>
            <div class="form-group col-md-4">
              <label for="provincia">Provincia</label>
              <input type="text" class="form-control" id="provincia" name="provincia" required>
            </div>
            <div class="form-group col-md-4">
              <label for="distrito">Distrito</label>
              <input type="text" class="form-control" id="distrito" name="distrito">
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="submit" form="formRegistrarSocio" class="btn btn-success">
          <i class="fas fa-save"></i> Guardar
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
$(document).ready(function () {
  $('#modalRegistrarSocio').on('hidden.bs.modal', function () {
    $('#formRegistrarSocio')[0].reset();
  });

  $('#modalRegistrarSocio').on('shown.bs.modal', function () {
    $('#dni').trigger('focus');
  });

  $('#formRegistrarSocio').off('submit').on('submit', function (event) {
    event.preventDefault();

    Swal.fire({
      title: '¿Estás seguro?',
      text: "¿Deseas registrar este socio?",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, registrar'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "POST",
          url: "modules/socio/procesar_socio.php",
          data: $(this).serialize(),
          dataType: "json",
          success: function (response) {
            if (response.success) {
              Swal.fire('¡Registrado!', response.message, 'success');
              $('#modalRegistrarSocio').modal('hide');
              $('#tabla-socios').DataTable().ajax.reload(null, false);
            } else {
              Swal.fire('Error', response.message, 'error');
            }
          },
          error: function () {
            Swal.fire('Error de conexión', 'No se pudo conectar con el servidor.', 'error');
          }
        });
      }
    });
  });
});
</script>
