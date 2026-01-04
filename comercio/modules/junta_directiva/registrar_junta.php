<?php
// Incluir archivos de conexión y otros elementos necesarios
require_once(__DIR__ . "/../../includes/conexion.php");

if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}

// Obtener los cargos
$cargos = [];
$query = "SELECT idcargo, tipo_cargo FROM cargo";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $cargos[] = $row;
}

// Obtener los grupos
$grupos = [];
$queryGrupos = "SELECT idgrupo, etiqueta_grupo, nombre_grupo FROM grupo";
$resultGrupos = $conn->query($queryGrupos);
while ($row = $resultGrupos->fetch_assoc()) {
    $grupos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registrar Junta Directiva</title>

    <!-- Incluir los estilos de Bootstrap y otros necesarios -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4><i class="fas fa-users"></i> Registrar Junta Directiva</h4>
                    </div>
                    <div class="card-body">
                        <form id="formJunta" action="modules/junta_directiva/procesar_junta.php" method="POST">
                            <div class="form-group">
                                <label for="grupo">Grupo:</label>
                                <select name="grupo" id="grupo" class="form-control" required>
                                    <option value="">Selecciona un grupo</option>
                                    <?php foreach ($grupos as $grupo): ?>
                                        <option value="<?= $grupo['idgrupo'] ?>"><?= $grupo['etiqueta_grupo'] ?> - <?= $grupo['nombre_grupo'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="fecha_inicio">Fecha de Inicio:</label>
                                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="fecha_fin">Fecha de Fin:</label>
                                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
                                </div>
                            </div>

                            <table class="table table-bordered mt-3" id="tabla-miembros">
                                <thead>
                                    <tr>
                                        <th>DNI</th>
                                        <th>Nombre</th>
                                        <th>Apellidos</th>
                                        <th>Celular</th>
                                        <th>Estado</th>
                                        <th>Cargo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="fila_1">
                                        <td><input type="text" name="dni_1" id="dni_1" class="form-control" onblur="autoComplete(1)" required></td>
                                        <td><input type="text" name="nombre_1" id="nombre_1" class="form-control" readonly></td>
                                        <td><input type="text" name="apellido_1" id="apellido_1" class="form-control" readonly></td>
                                        <td><input type="text" name="celular_1" id="celular_1" class="form-control" pattern="\d{9}" title="Ingrese 9 dígitos" oninput="validarCelular(this)"></td>
                                        <td><input type="text" name="estado_1" id="estado_1" class="form-control" readonly></td>
                                        <td>
                                            <select name="cargo_1" id="cargo_1" class="form-control" required>
                                                <option value="">Selecciona un cargo</option>
                                                <?php foreach ($cargos as $cargo): ?>
                                                    <option value="<?= $cargo['idcargo'] ?>"><?= $cargo['tipo_cargo'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><button type="button" class="btn btn-danger" onclick="eliminarFila(1)">Eliminar</button></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="text-left mb-3">
                                <button type="button" class="btn btn-primary" id="agregar-miembro">
                                    <i class="fas fa-user-plus"></i> Agregar Miembro
                                </button>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                                <a href="?pagina=junta_directiva/listar_junta" class="btn btn-primary">
                                    <i class="fas fa-list"></i> Ver Lista
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<script>
    $(document).ready(function () {
        // Evento para el envío del formulario
        $("#formJunta").submit(function (e) {
            e.preventDefault(); // Evita la recarga de página

            // Confirmación antes de proceder con el registro
            Swal.fire({
                title: '¿Estás seguro de que deseas registrar esta junta directiva?',
                text: "Los cambios no podrán deshacerse.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Verificar si hay DNI duplicados antes de enviar el formulario
                    const dnis = new Set();
                    let duplicado = false;

                    $('[id^="dni_"]').each(function () {
                        const dni = $(this).val();
                        if (dnis.has(dni)) {
                            duplicado = true;
                        }
                        dnis.add(dni);
                    });

                    if (duplicado) {
                        Swal.fire('Error', 'No puedes agregar un socio más de una vez.', 'error');
                    } else {
                        // Si el usuario confirma y no hay duplicados, enviamos el formulario
                        $.ajax({
                            url: 'modules/junta_directiva/procesar_junta.php',
                            type: 'POST',
                            data: $(this).serialize(),
                            dataType: 'json',
                            beforeSend: function () {
                                // Cerrar cualquier alerta previa antes de abrir una nueva
                                Swal.close(); // Cerrar alertas previas
                                
                                // Mostrar alerta de procesamiento
                                Swal.fire({
                                    title: 'Procesando...',
                                    text: 'Por favor espera...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                            success: function (response) {
                                // Cerrar el loading cuando se recibe la respuesta
                                Swal.close();

                                // Mostrar la respuesta según el éxito o error
                                if (response.status === "success") {
                                    Swal.fire('Éxito', response.message, 'success');
                                    $("#formJunta")[0].reset(); // Limpia el formulario si es exitoso
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function () {
                                // Cerrar el loading si hay un error
                                Swal.close();
                                Swal.fire('Error', 'Hubo un error al conectar con el servidor. Intenta nuevamente.', 'error');
                            }
                        });
                    }
                }
            });
        });
    });

    const cargos = <?= json_encode($cargos); ?>;

    // Función para calcular el estado según las fechas
    function calcularEstado() {
        const inicio = new Date(document.getElementById('fecha_inicio').value);
        const fin = new Date(document.getElementById('fecha_fin').value);
        const hoy = new Date();
        const estado = (inicio <= hoy && hoy <= fin) ? "Activo" : "Inactivo";

        document.querySelectorAll('[id^="estado_"]').forEach(el => el.value = estado);
    }

    // Event listeners para las fechas de inicio y fin
    document.getElementById('fecha_inicio').addEventListener('change', calcularEstado);
    document.getElementById('fecha_fin').addEventListener('change', calcularEstado);

    // Agregar un miembro a la lista
    $(document).ready(function () {
        let contador = 1;

        $("#agregar-miembro").click(function () {
            contador++;
            let opcionesCargo = '<option value="">Selecciona un cargo</option>';
            cargos.forEach(c => {
                opcionesCargo += `<option value="${c.idcargo}">${c.tipo_cargo}</option>`;
            });

            const nuevaFila = ` 
                <tr id="fila_${contador}">
                    <td><input type="text" name="dni_${contador}" id="dni_${contador}" class="form-control" onblur="autoComplete(${contador})" required></td>
                    <td><input type="text" name="nombre_${contador}" id="nombre_${contador}" class="form-control" readonly></td>
                    <td><input type="text" name="apellido_${contador}" id="apellido_${contador}" class="form-control" readonly></td>
                    <td><input type="text" name="celular_${contador}" id="celular_${contador}" class="form-control" pattern="\\d{9}" required></td>
                    <td><input type="text" name="estado_${contador}" id="estado_${contador}" class="form-control" readonly></td>
                    <td><select name="cargo_${contador}" id="cargo_${contador}" class="form-control" required>${opcionesCargo}</select></td>
                    <td><button type="button" class="btn btn-danger" onclick="eliminarFila(${contador})">Eliminar</button></td>
                </tr>`;
            $("#tabla-miembros tbody").append(nuevaFila);
            calcularEstado();
        });
    });

    // Función para eliminar una fila
    function eliminarFila(id) {
        $("#fila_" + id).remove();
    }

    // Función para autocompletar los datos de un socio por DNI
    function autoComplete(index) {
        const dni = $('#dni_' + index).val();
        const grupo = $('#grupo').val();

        if (!grupo) {
            Swal.fire('Atención', 'Debe seleccionar un grupo antes de buscar un socio.', 'warning');
            $('#dni_' + index).val('');
            return;
        }

        if (dni.length !== 8 || isNaN(dni)) {
            Swal.fire('Atención', 'El DNI debe tener 8 dígitos numéricos.', 'warning');
            $('#dni_' + index).val('');
            return;
        }

        let repetido = false;
        $('[id^="dni_"]').each(function () {
            if (this.id !== 'dni_' + index && $(this).val() === dni) {
                repetido = true;
            }
        });

        if (repetido) {
            Swal.fire('Advertencia', 'Este socio ya fue agregado.', 'warning');
            $('#dni_' + index).val('');
            return;
        }

        $.ajax({
            url: 'modules/junta_directiva/buscar_socio.php',
            type: 'GET',
            data: { dni: dni, grupo: grupo },
            beforeSend: function () {
                $('#nombre_' + index).val('Buscando...');
                $('#apellido_' + index).val('Buscando...');
            },
            success: function (response) {
                if (response.success) {
                    $('#nombre_' + index).val(response.nombre);
                    $('#apellido_' + index).val(response.apellido);
                    $('#celular_' + index).val(response.celular || '');
                } else {
                    Swal.fire('Error', response.error, 'error');
                    $('#dni_' + index).val('');
                    $('#nombre_' + index).val('');
                    $('#apellido_' + index).val('');
                    $('#celular_' + index).val('');
                }
            },
            error: function () {
                Swal.fire('Error', 'Error al conectar con el servidor.', 'error');
                $('#nombre_' + index).val('');
                $('#apellido_' + index).val('');
                $('#celular_' + index).val('');
            }
        });
    }
</script>

