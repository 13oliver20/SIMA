<!-- Modal para registrar cargo -->
<div class="modal fade" id="modalRegistrarCargo" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formRegistrarCargo">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Registrar Cargo</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="tipo_cargo">Tipo de Cargo</label>
            <input type="text" class="form-control" id="tipo_cargo" name="tipo_cargo" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Registrar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
$('#formRegistrarCargo').submit(function(e) {
    e.preventDefault();

    Swal.fire({
        title: '¿Deseas registrar este cargo?',
        text: "Verifica que los datos sean correctos.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, registrar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Si confirma, se envía el AJAX
            $.ajax({
                url: 'modules/cargo/procesar_cargo.php',
                method: 'POST',
                data: $('#formRegistrarCargo').serialize(),
                success: function(response) {
                    const res = JSON.parse(response);
                    if (res.success) {
                        $('#modalRegistrarCargo').modal('hide');
                        $('#formRegistrarCargo')[0].reset();
                        $('#tablaCargos').DataTable().ajax.reload(null, false);

                        Swal.fire({
                            icon: 'success',
                            title: '¡Registrado!',
                            text: 'El cargo se registró correctamente.',
                            confirmButtonText: 'Aceptar'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.error || 'Hubo un problema al registrar el cargo.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de servidor',
                        text: 'No se pudo procesar la solicitud.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }
    });
});
</script>
