<div class="card shadow">
    <!-- Cabecera con color hasta los bordes -->
    <div class="card-header bg-primary text-white rounded-0 text-center p-3">
        <h5 class="mb-0">Buscar Socio</h5>
    </div>
    <div class="card-body p-4">
        <form id="formBuscarSocio">
            <div class="form-group row">
                <!-- Etiqueta DNI -->
                <label for="dni" class="col-sm-2 col-form-label font-weight-bold text-right">DNI:</label>
                <div class="col-sm-4">
                    <input type="text" name="dni" id="dni" class="form-control" 
                        required maxlength="8" pattern="\d{8}" title="Ingrese un DNI válido de 8 dígitos"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                <!-- Botones alineados a la izquierda -->
                <div class="col-sm-6 d-flex">
                    <button type="submit" class="btn btn-primary mr-2">Buscar</button>
                     <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistrarSocio">
            <i class="fas fa-user-plus"></i> Nuevo Socio
        </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Aquí agregamos la clase mt-4 para darle espacio -->
<div id="resultado" class="mt-4"></div>

<!-- Incluir modal -->
<?php include 'modales/modal_registrosocio.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    // Manejar el envío del formulario
    document.getElementById("formBuscarSocio").addEventListener("submit", function(event) {
        event.preventDefault(); // Evitar recarga de la página
        
        let dni = document.getElementById("dni").value.trim();

        // Validación del DNI (debe ser de 8 dígitos)
        if (!/^\d{8}$/.test(dni)) {
            // Usar SweetAlert2 para mostrar un error de validación
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'Ingrese un DNI válido de 8 dígitos.',
            });
            return;
        }

        let resultadoDiv = document.getElementById("resultado");
        resultadoDiv.innerHTML = "<p class='text-info'>Buscando...</p>"; // Mostrar mensaje de búsqueda en curso

        // Hacer la solicitud fetch para buscar el socio
        fetch("modules/buscar_socio/buscar_socio_ajax.php?dni=" + dni)
            .then(response => response.json()) // Parsear la respuesta como JSON
            .then(data => {
                if (data.error) {
                    // Usar SweetAlert2 para mostrar un mensaje de error
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: data.error,
                    });
                    resultadoDiv.innerHTML = ''; // Limpiar contenido de resultados
                } else {
                    let socio = data.socio; // Datos del socio
                    let asociaciones = data.asociaciones; // Asociaciones del socio

                    // Crear el HTML para mostrar los datos del socio
                    let html = `
                        <div class="card mt-3 shadow-sm">
                            <div class="card-header bg-info text-white h5">Datos del Socio</div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr><th>DNI</th><td>${socio.dni}</td></tr>
                                    <tr><th>Nombre</th><td>${socio.nombre} ${socio.apellido_pat} ${socio.apellido_mat}</td></tr>
                                    <tr><th>Género</th><td>${socio.genero === 'F' ? 'Femenino' : (socio.genero === 'M' ? 'Masculino' : 'No especificado')}</td></tr>
                                    <tr><th>Departamento</th><td>${socio.departamento}</td></tr>
                                    <tr><th>Provincia</th><td>${socio.provincia}</td></tr>
                                    <tr><th>Distrito</th><td>${socio.distrito}</td></tr>
                                </table>
                            </div>
                        </div>`;

                    // Si el socio tiene asociaciones, agregarlas al HTML
                    if (asociaciones.length > 0) {
                        html += `
                        <div class="card mt-3 shadow-sm">
                            <div class="card-header bg-info text-white h5">Asociaciones del Socio</div>
                            <div class="table-responsive">
                               <table class="table table-sm-custom table-bordered">
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
                                // Agregar cada asociación al HTML
                                html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${asoc.nombre_grupo}</td>
                                <td>${asoc.ubicacion}</td>
                                <td>${asoc.cod_puesto ? asoc.cod_puesto : ''}</td>
                                <td>${asoc.rubro}</td>
                                <td>${asoc.categoria}</td>
                                <td>${asoc.agrupamiento}</td>
                                <td>${asoc.observacion}</td>
                                <td>${asoc.cargos_junta || 'SOCIO'}</td>
                                <td>${asoc.estado}</td>
                            </tr>`;
                            });
                        html += `</tbody></table></div></div>`;
                    }
                    // Actualizar el contenido con los datos
                    resultadoDiv.innerHTML = html;

                    // Usar SweetAlert2 para mostrar un mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Socio encontrado y cargado correctamente.',
                    });
                }
            })
            .catch(error => {
                // En caso de error en la solicitud
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Error al buscar socio.',
                });
                resultadoDiv.innerHTML = ''; // Limpiar contenido de resultados
            });
    });
});

</script>


