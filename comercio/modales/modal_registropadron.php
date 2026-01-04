<?php
require_once(__DIR__ . "/../includes/conexion.php");

if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}
?>

<!-- Modal -->
<div class="modal fade" id="modalPadron" tabindex="-1" role="dialog" aria-labelledby="modalPadronLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="modalPadronLabel">
          <i class="fas fa-file-upload"></i> Registrar Padrón de Socios
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
        <div id="mensaje" class="alert d-none"></div>

        <form id="formRegistrarPadron" enctype="multipart/form-data">
          
          <!-- Archivo -->
          <div class="form-group">
            <label for="archivo_padron">Archivo Padrón de Socios (PDF):</label>
            <input type="file" name="archivo_padron" id="archivo_padron" class="form-control" accept=".pdf" required>
            <small class="form-text text-muted">Solo archivos en formato PDF.</small>
          </div>

          <!-- Grupo -->
          <div class="form-group">
            <label for="grupo_id">Grupo:</label>
            <div class="d-flex">
              <select name="grupo_id" id="grupo_id" class="form-control w-100" required>
                <option value="">Selecciona un grupo</option>
                <?php
                $query = "SELECT idgrupo, etiqueta_grupo, nombre_grupo FROM grupo";
                $result = $conn->query($query);
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $idgrupo = $row['idgrupo'];
                        $etiqueta = htmlspecialchars($row['etiqueta_grupo']);
                        $nombre = htmlspecialchars($row['nombre_grupo']);
                        echo "<option value='{$idgrupo}'>{$etiqueta} - {$nombre}</option>";
                    }
                } else {
                    echo "<option disabled>No hay grupos disponibles</option>";
                }
                ?>
              </select>
            </div>
          </div>

          <!-- Botones -->
          <div class="d-flex justify-content-end">
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

<!-- Script para AJAX y validación con SweetAlert2 -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formRegistrarPadron");
    const archivoInput = document.getElementById("archivo_padron");

    // Validación del archivo
    archivoInput.addEventListener("change", function () {
        const file = this.files[0];
        if (file && file.type !== "application/pdf") {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Solo se permiten archivos PDF.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Aceptar'
            });
            this.value = "";
        }
    });

    // Envío del formulario
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Deseas registrar este padrón de socios?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData(form);

                // Mostrar cargando
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Por favor, espera un momento.',
                    icon: 'info',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch("modules/padron_socios/procesar_padron.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();

                    if (data.status === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: data.message,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            // Limpiar formulario y cerrar modal
                            form.reset();
                            const modal = bootstrap.Modal.getInstance(document.getElementById('modalPadron'));
                            if (modal) modal.hide();

                            // Recargar DataTable sin recargar página
                            if (typeof tabla !== 'undefined') {
                                tabla.ajax.reload(null, false);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                })
                .catch(() => {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema con la solicitud. Por favor, inténtalo de nuevo.',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Aceptar'
                    });
                });
            }
        });
    });
});
</script>
