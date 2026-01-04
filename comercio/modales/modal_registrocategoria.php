<?php
require_once(__DIR__ . "/../includes/conexion.php");

if (!$conn) {
    echo "<div class='alert alert-danger'>Error: No se pudo conectar a la base de datos.</div>";
    exit;
}
?>

<!-- Modal -->
<div class="modal fade" id="modalRegistrarCategoria" tabindex="-1" role="dialog" aria-labelledby="modalRegistrarCategoriaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      <!-- Encabezado -->
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalRegistrarCategoriaLabel"><i class="fas fa-plus"></i> Registrar Nueva Categoría</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Cuerpo del modal -->
      <div class="modal-body">
        <form id="registrarCategoriaForm" method="POST">
          <div class="form-group">
            <label for="tipo">Tipo de Categoría:</label>
            <input type="text" id="tipo" name="tipo" class="form-control" required>
          </div>
        </form>
      </div>

      <!-- Pie del modal -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times"></i> Cancelar
        </button>
        <button type="submit" form="registrarCategoriaForm" class="btn btn-success">
          <i class="fas fa-save"></i> Guardar
        </button>
      </div>

    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    $("#registrarCategoriaForm").submit(function(event) {
        event.preventDefault(); // Evitar envío inmediato

        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Deseas registrar esta categoría?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                enviarFormulario();
            }
        });
    });

    function enviarFormulario() {
        let formData = $("#registrarCategoriaForm").serialize();

        $.ajax({
            url: "modules/categoria/procesar_categoria.php",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                mostrarAlerta(response.status, response.message);

                if (response.status === "success") {
                    $("#registrarCategoriaForm")[0].reset();           // Limpiar formulario
                    $('#modalRegistrarCategoria').modal('hide');      // Cerrar modal
                    $('#tabla-categorias').DataTable().ajax.reload(null, false); // Recargar tabla
                }
            },
            error: function() {
                mostrarAlerta("danger", "Error al procesar la solicitud.");
            }
        });
    }

    function mostrarAlerta(tipo, mensaje) {
        let icono = "info";
        if (tipo === "success") icono = "success";
        else if (tipo === "danger") icono = "error";
        else if (tipo === "warning") icono = "warning";

        Swal.fire({
            icon: icono,
            title: mensaje,
            confirmButtonText: 'Aceptar',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false
        });
    }
});
</script>
