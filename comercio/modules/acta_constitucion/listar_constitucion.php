<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Actas de Constitución</title>
      <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        table.dataTable {
            font-size: clamp(12px, 1vw, 14px);
            width: 100% !important;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fas fa-list"></i> Listado de Actas de Constitución</h2>
    <div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistrarActa">
    <i class="fas fa-file-alt"></i> Registrar Acta
</button>
</div>

    <table id="tabla-actas" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Nº</th>
                <th>Código</th>
                <th>Grupo</th>
                <th>Fecha de Fundación</th>
                <th>Archivo</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Incluir modal -->
<?php include 'modales/modal_registroacta.php'; ?>

<script>
$(document).ready(function() {
    // Definir los idiomas disponibles para DataTables
    const languageOptions = {
        es: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
        en: "//cdn.datatables.net/plug-ins/1.13.6/i18n/en-GB.json"
    };

    let currentLanguage = 'es'; // Idioma por defecto

    // Configurar DataTable
    let tabla = $('#tabla-actas').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "modules/acta_constitucion/cargar_constitucion.php",
            "type": "POST"
        },
        "columns": [
            { "data": "idacta_constitucion" },
            { "data": "etiqueta_grupo" },
            { "data": "nombre_grupo" },
            { "data": "fecha_fundacion" },
            { "data": "archivo_acta",
              "render": function(data) {
                  return data ? `<a href="uploads/constitucion/${data}" target="_blank">
                                    <i class="fas fa-file-pdf text-danger"></i>
                                 </a>` : "-";
              }
            },
            { "data": "idacta_constitucion",  
              "render": function(data) {
                  return `
                      <div class="btn-group">
                          <button class="btn btn-primary btn-sm" onclick="editarActa(${data})">
                              <i class="fas fa-edit"></i>
                          </button>
                          <button class="btn btn-danger btn-sm" onclick="confirmarEliminar(${data})">
                              <i class="fas fa-trash-alt"></i>
                          </button>
                      </div>
                  `;
              }
            }
        ],
        language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
        "lengthMenu": [[5, 10, 15, -1], [5, 10, 15, "Todos"]]
    });
});

$(document).on('click', '.btnEliminar', function () {
    var id = $(this).data('id');

    Swal.fire({
        title: '¿Eliminar Acta?',
        text: "Esta acción eliminará el acta y su archivo PDF.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'modules/acta_constitucion/eliminar_constitucion.php',
                type: 'POST',
                data: { id: id },
                success: function (respuesta) {
                    if (respuesta === 'success') {
                        Swal.fire(
                            '¡Eliminado!',
                            'El acta fue eliminada correctamente.',
                            'success'
                        );
                        $('#tablaActa').DataTable().ajax.reload(); // actualiza la tabla
                    } else if (respuesta === 'not_found') {
                        Swal.fire('Error', 'Acta no encontrada.', 'error');
                    } else {
                        Swal.fire('Error', 'No se pudo eliminar el acta.', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Error en la conexión con el servidor.', 'error');
                }
            });
        }
    });
});
</script>

</body>
</html>
