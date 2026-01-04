<?php
require_once 'auth.php';
require_once "includes/header.php";
?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white rounded-0 text-center p-3">
            <h5 class="mb-0 font-weight-bold">Buscar Socio</h5>
        </div>
        <div class="card-body p-4">
            <form id="formBuscarSocio" class="needs-validation" novalidate>
                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <label for="dni" class="sr-only">DNI</label>
                        <input type="text" name="dni" id="dni" class="form-control form-control-lg"
                            placeholder="Ingrese DNI (8 dígitos)" required maxlength="8" pattern="\d{8}"
                            title="Ingrese un DNI válido de 8 dígitos" autocomplete="off"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <div class="invalid-feedback">
                            Por favor, ingrese un DNI válido de 8 dígitos.
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-success btn-lg px-4" data-toggle="modal" data-target="#modalRegistrarSocio">
                            <i class="fas fa-user-plus"></i> Nuevo Socio
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="resultado" class="mt-4"></div>
</div>

<?php include 'modales/modal_registrosocio.php'; ?>
<?php require_once "includes/footer.php"; ?>

<style>
    /* Mejoras para la tabla de asociaciones */
    #resultado table {
        font-size: 0.9rem;
    }
    #resultado table thead th {
        background-color: #4e73df;
        color: white;
        text-align: center;
        vertical-align: middle;
        border-color: #4e73df;
    }
    #resultado table tbody td {
        vertical-align: middle;
        text-align: center;
    }
    #resultado .card-header h5 {
        font-weight: 700;
        letter-spacing: 0.03em;
    }
</style>

<script>
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var form = document.getElementById('formBuscarSocio');
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        }, false);
    })();

    function validarDNI(dni) {
        return /^\d{8}$/.test(dni);
    }

    function construirHTMLSocio(socio, asociaciones) {
        let html = `
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-user"></i> Datos del Socio</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 font-weight-bold text-primary">DNI:</div>
                    <div class="col-md-8">${socio.dni}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 font-weight-bold text-primary">Nombre Completo:</div>
                    <div class="col-md-8">${socio.nombre} ${socio.apellido_pat} ${socio.apellido_mat}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 font-weight-bold text-primary">Género:</div>
                    <div class="col-md-8">${socio.genero === 'F' ? 'Femenino' : (socio.genero === 'M' ? 'Masculino' : 'No especificado')}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 font-weight-bold text-primary">Departamento:</div>
                    <div class="col-md-8">${socio.departamento}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 font-weight-bold text-primary">Provincia:</div>
                    <div class="col-md-8">${socio.provincia}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 font-weight-bold text-primary">Distrito:</div>
                    <div class="col-md-8">${socio.distrito}</div>
                </div>
            </div>
        </div>`;

        if (asociaciones.length > 0) {
            html += `
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Asociaciones del Socio</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>Grupo</th>
                                <th>Ubicación</th>
                                <th>Código Puesto</th>
                                <th>Rubro</th>
                                <th>Categoría</th>
                                <th>Agrupamiento</th>
                                <th>Observación</th>
                                <th>Cargo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>`;
            asociaciones.forEach((asoc, index) => {
                html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${asoc.nombre_grupo}</td>
                    <td>${asoc.ubicacion}</td>
                    <td>${asoc.cod_puesto || ''}</td>
                    <td>${asoc.rubro}</td>
                    <td>${asoc.categoria}</td>
                    <td>${asoc.agrupamiento}</td>
                    <td>${asoc.observacion || ''}</td>
                    <td>${asoc.cargos_junta || 'SOCIO'}</td>
                    <td>${asoc.estado}</td>
                </tr>`;
            });
            html += `</tbody></table></div></div>`;
        } else {
            html += `
            <div class="alert alert-warning mt-4" role="alert">
                <i class="fas fa-exclamation-triangle"></i> Este socio no tiene asociaciones registradas.
            </div>`;
        }
        return html;
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("formBuscarSocio").addEventListener("submit", function(event) {
            event.preventDefault();

            let dni = document.getElementById("dni").value.trim();

            if (!validarDNI(dni)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ingrese un DNI válido de 8 dígitos.',
                });
                return;
            }

            let resultadoDiv = document.getElementById("resultado");
            resultadoDiv.innerHTML = `<div class="text-center text-info my-4">
                <div class="spinner-border" role="status"><span class="sr-only">Cargando...</span></div>
                <div>Cargando información del socio...</div>
            </div>`;

            fetch(`modules/buscar_socio/buscar_socio_ajax.php?dni=${dni}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'No encontrado',
                            text: data.error,
                        });
                        resultadoDiv.innerHTML = '';
                    } else {
                        resultadoDiv.innerHTML = construirHTMLSocio(data.socio, data.asociaciones);
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al buscar el socio. Intente nuevamente.',
                    });
                    resultadoDiv.innerHTML = '';
                });
        });
    });
</script>
