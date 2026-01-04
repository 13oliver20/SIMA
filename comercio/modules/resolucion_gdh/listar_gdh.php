<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Resoluciones</title>
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
    <h2><i class="fas fa-list"></i> Listado de Resoluciones</h2>
    <div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalResolucion">
    <i class="fas fa-file-alt"></i> Resolucion GDH
</button>
</div>
    <table id="tabla-resoluciones" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Nº</th>
                <th>Código</th>
                <th>Grupo</th>
                <th>Nº Resolución GDH</th>
                <th>Fecha</th>
                <th>Archivo</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Incluir modal -->
<?php include 'modales/modal_registroresolucion.php'; ?>

<script>
$(document).ready(function() {
    let tabla = $('#tabla-resoluciones').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "modules/resolucion_gdh/cargar_resoluciones.php",
            "type": "POST"
        },
        "columns": [
            { "data": "idresolucion_gdh" },
            { "data": "etiqueta_grupo" },
            { "data": "nombre_grupo" },
            { "data": "num_resolucion" },
            { "data": "fecha_emision" },
            { "data": "archivo_gdh",
              "render": function(data) {
                  return data ? `<a href="uploads/resolucion/${data}" target="_blank">
                                    <i class="fas fa-file-pdf text-danger"></i>
                                 </a>` : "-";
              }
            },
            { "data": "idresolucion_gdh",  
              "render": function(data) {
                  return `
                      <div class="btn-group">
                          <button class="btn btn-primary btn-sm" onclick="editarResolucion(${data})">
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


// ABRIR MODAL DE EDICIÓN
function editarResolucion(id) {
    $("#edit-id").val(id);

    $.get("modules/resolucion_gdh/get_resolucion.php", { id: id }, function(data) {
        $("#edit-num_resolucion").val(data.num_resolucion);
        $("#edit-fecha_emision").val(data.fecha_emision);
        $("#modal-editar").show();
    }, "json");
}

// GUARDAR EDICIÓN
$("#editar-form").submit(function(e) {
    e.preventDefault();
    $.post("modules/resolucion_gdh/procesar_edicion.php", $(this).serialize(), function() {
        Swal.fire("¡Actualizado!", "La resolución se ha editado correctamente.", "success");
        $("#modal-editar").hide();
        $('#tabla-resoluciones').DataTable().ajax.reload();
    });
});

// CERRAR MODAL
function cerrarModal() {
    $("#modal-editar").hide();
}

// ELIMINAR RESOLUCIÓN
function confirmarEliminar(id) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("modules/resolucion_gdh/eliminar_resolucion.php", { id: id }, function() {
                Swal.fire("¡Eliminado!", "La resolución ha sido eliminada.", "success");
                $('#tabla-resoluciones').DataTable().ajax.reload();
            });
        }
    });
}
</script>

</body>
</html>
