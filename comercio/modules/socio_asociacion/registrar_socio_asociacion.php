<?php
session_start();
require_once(__DIR__ . "/../../includes/conexion.php");
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}

// Obtener asociaciones
$query_grupo = "SELECT idgrupo, etiqueta_grupo, nombre_grupo FROM grupo ORDER BY idgrupo";
$result_grupo = $conn->query($query_grupo);

// Obtener rubros
$query_rubros = "SELECT ss.idsubrubro_seg, rp.nombre AS rubro, sp.nombre AS subrubro1, ss.nombre AS subrubro2
FROM subrubro_segundo ss
JOIN subrubro_primero sp ON ss.subrubro_primero_idsubrubro = sp.idsubrubro
JOIN rubro_principal rp ON sp.rubro_principal_idrubro = rp.idrubro
ORDER BY ss.idsubrubro_seg";
$result_rubros = $conn->query($query_rubros);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asociar Socio</title>
</head>
<body>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">Buscar Socio</div>
        <div class="card-body">
        <form id="buscarSocioForm" class="form-inline">
    <label for="dni" class="mr-2">DNI:</label>
    <input type="text" name="dni" id="dni" class="form-control form-control-sm mr-2" required maxlength="8" pattern="\d{8}" style="width: 200px;">
    <button type="submit" class="btn btn-primary btn-sm mr-2">Buscar</button>
    <!-- Botón Nuevo Socio colocado al costado del botón Buscar -->
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrarSocio">
        <i class="fas fa-user-plus"></i> Nuevo Socio
    </button>
</form>
        </div>
    </div>

    <div id="socio-info" class="mt-3">
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

    <div id="form-container" class="mt-3">
        <div class="card">
            <div class="card-header bg-success text-white">Asociar Socio</div>
            <div class="card-body">
                <form id="asociarSocioForm">
                    <input type="hidden" name="idsocio" id="idsocio">

                    <div class="form-group">
                        <label for="idasociacion">Asociación:</label>
                        <select name="idasociacion" id="idasociacion" class="form-control form-control-sm" required>
                            <option value="">-- Selecciona --</option>
                            <?php while ($asociacion = $result_grupo->fetch_assoc()) { ?>
                                <option value="<?= $asociacion['idgrupo'] ?>">
                                    <?= htmlspecialchars($asociacion['etiqueta_grupo'] . " - " . $asociacion['nombre_grupo']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_rubro">Rubro:</label>
                        <select name="id_rubro" id="id_rubro" class="form-control form-control-sm" required>
                            <option value="">-- Selecciona --</option>
                            <?php while ($rubro = $result_rubros->fetch_assoc()) { ?>
                                <option value="<?= $rubro['idsubrubro_seg'] ?>">
                                    <?= htmlspecialchars($rubro['idsubrubro_seg'] . " - " . $rubro['rubro'] . " - " . $rubro['subrubro1'] . " - " . $rubro['subrubro2']) ?>
                                </option>
                            <?php } ?>
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
                        <a href="?pagina=socio_asociacion/listar_asociados" class="btn btn-primary">
                            <i class="fas fa-list"></i> Ver Lista
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Incluir modal -->
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

    $("#buscarSocioForm").submit(function(event) {
        event.preventDefault();
        let dni = $("#dni").val().trim();

        $.ajax({
            url: "modules/socio_asociacion/buscar_socios.php",
            type: "GET",
            data: { dni: dni },
            dataType: "json",
            success: function(response) {
                if (response.error) {
                    mostrarAlerta("error", response.error);
                } else {
                    $("#idsocio").val(response.idsocio);
                    $("#nombre").text(response.nombre);
                    $("#apellido_pat").text(response.apellido_pat);
                    $("#apellido_mat").text(response.apellido_mat);
                    $("#genero").text(response.genero);
                    $("#departamento").text(response.departamento);
                    $("#provincia").text(response.provincia);
                    $("#distrito").text(response.distrito);

                    if (response.total_asociaciones > 0) {
                        mostrarAlerta("error", `El socio ya pertenece a ${response.total_asociaciones} asociación(es).`);
                    } else {
                        mostrarAlerta("success", "Socio encontrado.");
                    }
                }
            },
            error: function() {
                mostrarAlerta("error", "Error en la búsqueda.");
            }
        });
    });

    $("#asociarSocioForm").submit(function(event) {
        event.preventDefault();

        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Deseas asociar este socio?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                let formData = new FormData(document.getElementById("asociarSocioForm"));

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
                        }
                    },
                    error: function() {
                        mostrarAlerta("error", "❌ Error al procesar la solicitud.");
                    }
                });
            }
        });
    });

    $("#activar_cod_puesto").change(function() {
        $("#cod_puesto").prop("disabled", !this.checked).val(this.checked ? "" : "");
    });
</script>

</body>
</html>

<?php $conn->close(); ?>
