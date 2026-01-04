<?php
ob_start();
require_once "includes/header.php";
require_once 'auth.php';
?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Tabla de junta directiva -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h2 class="mb-4">Carga masiva de DNIs para reporte de duplicidad</h2>

            <form id="formularioExcel" enctype="multipart/form-data" method="POST" class="mb-3">
                <div class="form-group">
                    <input type="file" class="form-control-file" id="archivo" name="archivo" accept=".xls,.xlsx" required>
                </div>
                <button type="submit" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">Procesar</button>
            </form>

            <div id="descarga" style="display: none;">
                <a href="#" download class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" id="btnDescargar">
                    <i class="fas fa-download"></i> Descargar
                </a>
            </div>
        </div>
    </div>
</div>
<?php 
require_once "includes/footer.php";
?>

<script>
$(document).ready(function() {
    let xhr; // para abortar la subida

    $('#formularioExcel').on('submit', function(e) {
        e.preventDefault();

        var archivo = $('#archivo')[0].files[0];
        if (!archivo) {
            Swal.fire('Error', 'Selecciona un archivo.', 'error');
            return;
        }

        var allowedTypes = [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        if (!allowedTypes.includes(archivo.type)) {
            Swal.fire('Error', 'Solo se permiten archivos Excel (.xls, .xlsx).', 'error');
            return;
        }

        var maxSize = 5 * 1024 * 1024; // 5MB
        if (archivo.size > maxSize) {
            Swal.fire('Error', 'El archivo es demasiado grande. Máximo 5MB.', 'error');
            return;
        }

        var formData = new FormData(this);

        xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                let percent = Math.round((e.loaded / e.total) * 100);
                $('#progressBar').css('width', percent + '%').text(percent + '%');
            }
        });

        xhr.addEventListener('load', function() {
            Swal.close();
            try {
                let data = JSON.parse(xhr.responseText);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        html: `
                            <p>${data.message}</p>
                            <p><strong>Total DNIs:</strong> ${data.total}</p>
                            <p><strong>Válidos:</strong> ${data.validos}</p>
                            <p><strong>Inválidos:</strong> ${data.invalidos}</p>
                        `
                    });
                    $('#btnDescargar').attr('href', data.ruta);
                    $('#descarga').show();
                } else {
                    Swal.fire('Error', data.message, 'error');
                    $('#descarga').hide();
                }
            } catch {
                Swal.fire('Error', 'Respuesta inválida del servidor.', 'error');
                $('#descarga').hide();
            }
        });

        xhr.addEventListener('error', function() {
            Swal.close();
            Swal.fire('Error', 'Hubo un problema al subir el archivo.', 'error');
            $('#descarga').hide();
        });

        xhr.open('POST', 'modules/subir_dni/cargar_dni.php', true);

        Swal.fire({
            title: 'Subiendo archivo...',
            html: `<div class="progress">
                     <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%;">0%</div>
                   </div>
                   <button id="cancelUpload" class="btn btn-danger mt-3">Cancelar</button>`,
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                document.getElementById('cancelUpload').addEventListener('click', () => {
                    xhr.abort();
                    Swal.close();
                    Swal.fire('Cancelado', 'La carga ha sido cancelada.', 'info');
                });
                xhr.send(formData);
            }
        });
    });
});
</script>
