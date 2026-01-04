<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Padrón de Socios</title>

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
    <h2><i class="fas fa-list"></i> Listado de Padrón de Socios</h2>
    <div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPadron">
    <i class="fas fa-file-alt"></i> Padron de socios
</button>
</div>
    <table id="tabla-padron" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Nº</th>
                <th>Grupo</th>
                <th>Archivo</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Incluir modal -->
<?php include 'modales/modal_registroPadron.php'; ?>

<script>
$(document).ready(function() {
    $('#tabla-padron').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "modules/padron_socios/cargar_padron.php", // URL al script PHP
            "type": "POST"
        },
        "columns": [
            { "data": "idpadron_socios" },
            { "data": "nombre_grupo" }, 
            { "data": "archivo_padron",
              "render": function(data) {
                  return data ? `<a href="uploads/padron/${data}" target="_blank">
                                    <i class="fas fa-file-pdf text-danger"></i>
                                 </a>` : "-";
              }
            },
            { "data": "acciones",  
              "render": function(data) {
                  return ` 
                      <div class="btn-group">
                          <button class="btn btn-primary btn-sm" onclick="editarPadron(${data})">
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

// Función para editar
function editarPadron(id) {
    alert("Editar padrón: " + id);
}

// Confirmar eliminación con SweetAlert2
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
            $.post("modules/padron_socios/eliminar_padron.php", { id: id }, function(response) {
                Swal.fire("¡Eliminado!", "El padrón ha sido eliminado.", "success");
                $('#tabla-padron').DataTable().ajax.reload();
            });
        }
    });
}
</script>

</body>
</html>
