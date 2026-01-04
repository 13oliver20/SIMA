<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Vigencias de Poder</title>
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
    <h2><i class="fas fa-list"></i> Listado de Vigencias de Poder</h2>
    <!-- Botón para abrir el modal -->
    <div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalVigencia">
    <i class="fas fa-file-alt"></i> Vigencia de Poder
</button>
</div>
    <table id="tabla-vigencias" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Nº</th>
                <th>Código</th>
                <th>Grupo</th>
                <th>Partida Registral</th>
                <th>Archivo</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Incluir modal -->
<?php include 'modales/modal_registrovigencia.php'; ?>

<script>
$(document).ready(function() {
    $('#tabla-vigencias').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "modules/vigencia_poder/cargar_vigencia.php", // Ruta a tu archivo PHP
            "type": "POST"
        },
        "columns": [
            { "data": "idvigencia_poder" },
            { "data": "etiqueta_grupo" },
            { "data": "nombre_grupo" },
            { "data": "partida_registral" },
            { "data": "archivo_vigencia",
              "render": function(data) {
                  return data ? `<a href="uploads/vigencia/${data}" target="_blank"><i class="fas fa-file-pdf text-danger"></i></a>` : "-";
              }
            },
            { "data": "acciones",
              "render": function(data) {
                  return `
                      <div class="btn-group">
                          <button class="btn btn-primary btn-sm" onclick="editarVigencia(${data})">
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
function editarVigencia(id) {
    alert("Editar vigencia: " + id);
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
            $.post("modules/vigencia_poder/eliminar_vigencia.php", { id: id }, function(response) {
                Swal.fire("¡Eliminado!", "La vigencia ha sido eliminada.", "success");
                $('#tabla-vigencias').DataTable().ajax.reload();
            });
        }
    });
}

</script>

</body>
</html>
