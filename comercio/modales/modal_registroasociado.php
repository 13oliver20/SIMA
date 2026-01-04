<?php
require_once(__DIR__ . "/../includes/conexion.php");

// Validar conexión
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}

// Obtener asociaciones
$query_grupo = "SELECT idgrupo, etiqueta_grupo, nombre_grupo FROM grupo ORDER BY idgrupo";
$result_grupo = $conn->query($query_grupo);

// Obtener rubros
$query_rubros = "
    SELECT ss.idsubrubro_seg, rp.nombre AS rubro, sp.nombre AS subrubro1, ss.nombre AS subrubro2
    FROM subrubro_segundo ss
    JOIN subrubro_primero sp ON ss.subrubro_primero_idsubrubro = sp.idsubrubro
    JOIN rubro_principal rp ON sp.rubro_principal_idrubro = rp.idrubro
    ORDER BY ss.idsubrubro_seg
";
$result_rubros = $conn->query($query_rubros);
?>

<!-- Modal Buscar y Asociar Socio -->
<div class="modal fade" id="modalAsociarSocio" tabindex="-1" role="dialog" aria-labelledby="modalAsociarSocioLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalAsociarSocioLabel">Asociar Socio</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <!-- Buscar Socio -->
        <div class="card mb-3">
          <div class="card-header bg-primary text-white">Buscar Socio</div>
          <div class="card-body">
            <form id="buscarSocioForm" class="form-inline" autocomplete="off">
              <label for="dni" class="mr-2">DNI:</label>
              <input type="text" name="dni" id="dni" class="form-control form-control-sm mr-2" required maxlength="8" pattern="\d{8}" style="width: 200px;" title="Ingrese 8 dígitos numéricos">
              <button type="submit" class="btn btn-primary btn-sm mr-2">Buscar</button>
              <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalRegistrarSocio">
                <i class="fas fa-user-plus"></i> Nuevo Socio
              </button>
            </form>
          </div>
        </div>

        <!-- Datos del Socio -->
        <div id="socio-info" class="mb-3" style="display:none;">
          <div class="card">
            <div class="card-header bg-info text-white text-center">
              <h5 class="mb-0">Datos del Socio</h5>
            </div>
            <div class="card-body">
              <div class="row mb-2">
                <div class="col-md-3"><strong>Nombre:</strong> <span id="nombre"></span></div>
                <div class="col-md-3"><strong>Apellido Paterno:</strong> <span id="apellido_pat"></span></div>
                <div class="col-md-3"><strong>Apellido Materno:</strong> <span id="apellido_mat"></span></div>
                <div class="col-md-3"><strong>Género:</strong> <span id="genero"></span></div>
              </div>
              <div class="row">
                <div class="col-md-3"><strong>Departamento:</strong> <span id="departamento"></span></div>
                <div class="col-md-3"><strong>Provincia:</strong> <span id="provincia"></span></div>
                <div class="col-md-3"><strong>Distrito:</strong> <span id="distrito"></span></div>
                <div class="col-md-3"><strong>Otra Info:</strong> <span id="extra"></span></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Formulario de Asociación -->
        <div id="form-container" style="display:none;">
          <div class="card">
            <div class="card-header bg-success text-white">Asociar Socio</div>
            <div class="card-body">
              <form id="asociarSocioForm" autocomplete="off">
                <input type="hidden" name="idsocio" id="idsocio">

                <div class="form-group">
                  <label for="idasociacion">Asociación:</label>
                  <select name="idasociacion" id="idasociacion" class="form-control form-control-sm" required>
                    <option value="">-- Selecciona --</option>
                    <?php while ($asociacion = $result_grupo->fetch_assoc()) : ?>
                      <option value="<?= htmlspecialchars($asociacion['idgrupo']) ?>">
                        <?= htmlspecialchars($asociacion['etiqueta_grupo'] . " - " . $asociacion['nombre_grupo']) ?>
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>

                <div class="form-group">
                  <label for="id_rubro">Rubro:</label>
                  <select name="id_rubro" id="id_rubro" class="form-control form-control-sm" required>
                    <option value="">-- Selecciona --</option>
                    <?php while ($rubro = $result_rubros->fetch_assoc()) : ?>
                      <option value="<?= htmlspecialchars($rubro['idsubrubro_seg']) ?>">
                        <?= htmlspecialchars($rubro['idsubrubro_seg'] . " - " . $rubro['rubro'] . " - " . $rubro['subrubro1'] . " - " . $rubro['subrubro2']) ?>
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="cod_puesto">Código de Puesto:</label>
                    <div class="form-check">
                      <input type="checkbox" id="activar_cod_puesto" class="form-check-input">
                      <label for="activar_cod_puesto" class="form-check-label">Pertenece a un mercado</label>
                    </div>
                    <input type="text" name="cod_puesto" id="cod_puesto" class="form-control form-control-sm" placeholder="Código de Puesto" disabled>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="observacion">Observación:</label>
                    <input type="text" name="observacion" id="observacion" class="form-control form-control-sm" placeholder="Observaciones">
                  </div>
                </div>

                <div class="d-flex justify-content-between">
                  <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div> <!-- /modal-body -->

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Incluir modal de registro socio -->
<?php include 'modales/modal_registrosocio.php'; ?>

<script>
function mostrarAlerta(tipo, mensaje) {
    Swal.fire({
        icon: tipo,
        title: tipo === "success" ? "¡Éxito!" : tipo === "error" ? "¡Error!" : "Aviso",
        text: mensaje,
        timer: 3000,
        showConfirmButton: false
    });
}

// Mostrar/ocultar datos y formulario según búsqueda
function mostrarDatosSocio(datos) {
    if(datos) {
        $("#socio-info").show();
        $("#form-container").show();

        $("#idsocio").val(datos.idsocio);
        $("#nombre").text(datos.nombre);
        $("#apellido_pat").text(datos.apellido_pat);
        $("#apellido_mat").text(datos.apellido_mat);
        $("#genero").text(datos.genero);
        $("#departamento").text(datos.departamento);
        $("#provincia").text(datos.provincia);
        $("#distrito").text(datos.distrito);
        $("#extra").text(datos.extra || '');
    } else {
        $("#socio-info").hide();
        $("#form-container").hide();
        $("#idsocio").val('');
        $("#nombre, #apellido_pat, #apellido_mat, #genero, #departamento, #provincia, #distrito, #extra").text('');
    }
}

// Buscar socio por DNI
$("#buscarSocioForm").submit(function(event) {
    event.preventDefault();
    let dni = $("#dni").val().trim();

    if(!dni.match(/^\d{8}$/)) {
        mostrarAlerta("error", "Por favor ingresa un DNI válido de 8 dígitos.");
        return;
    }

    $.ajax({
        url: "modules/socio_asociacion/buscar_socios.php",
        type: "GET",
        data: { dni: dni },
        dataType: "json",
        success: function(response) {
            if (response.error) {
                mostrarAlerta("error", response.error);
                mostrarDatosSocio(null);
            } else {
                mostrarDatosSocio(response);

                if (response.total_asociaciones > 0) {
                    mostrarAlerta("error", `El socio ya pertenece a ${response.total_asociaciones} asociación(es).`);
                } else {
                    mostrarAlerta("success", "Socio encontrado.");
                }
            }
        },
        error: function() {
            mostrarAlerta("error", "Error en la búsqueda.");
            mostrarDatosSocio(null);
        }
    });
});

// Asociar socio
$("#asociarSocioForm").submit(function(event) {
    event.preventDefault();

    if (!$("#idsocio").val()) {
        mostrarAlerta("error", "Primero debes buscar y seleccionar un socio válido.");
        return;
    }

    Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Deseas asociar este socio?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData(this);

            $.ajax({
                url: "modules/socio_asociacion/procesar_socio_asociacion.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(response) {
                    mostrarAlerta(response.status === "success" ? "success" : "error", response.message);
                    if (response.status === "success") {
                        $("#asociarSocioForm")[0].reset();
                        $("#buscarSocioForm")[0].reset();
                        mostrarDatosSocio(null);

                        // Recargar tabla DataTable
                        if (typeof tablaSociosAsociados !== 'undefined') {
                            tablaSociosAsociados.ajax.reload(null, false);
                        }
                    }
                },
                error: function() {
                    mostrarAlerta("error", "❌ Error al procesar la solicitud.");
                }
            });
        }
    });
});

// Activar/desactivar input de código de puesto
$("#activar_cod_puesto").change(function() {
    $("#cod_puesto").prop("disabled", !this.checked).val(this.checked ? "" : "");
});
</script>
