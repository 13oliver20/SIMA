<?php
require_once(__DIR__ . "/../../includes/conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Socios en Asociaciones</title>

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
    <h2><i class="fas fa-users"></i> Lista de Asociados</h2>
    <table id="tabla-socios" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Nº</th>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>G</th>
                <th>Grupo</th>
                <th>Agrupamiento</th>
                <th>Categoría</th>
                <th>Código Puesto</th>
                <th>Subrubro</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<script>
$(document).ready(function() {
    var tabla = $('#tabla-socios').DataTable({
        "processing": true,
        "serverSide": false, // Ahora no usaremos serverSide, ya que vamos a cargar todos los datos de una vez
        "ajax": {
            "url": "modules/socio_asociacion/cargar_asociados.php", // Aquí está el archivo que hace la consulta
            "type": "POST"
        },
        "columns": [
            { "data": "num" },
            { "data": "dni" },
            { "data": "nombre" },
            { "data": "apellido_pat" },
            { "data": "apellido_mat" },
            { "data": "genero" },
            { "data": "grupo" },
            { "data": "agrupamiento" },
            { "data": "categoria" },
            { "data": "puesto" },
            { "data": "rubro" },
            { "data": "acciones", "orderable": false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        "lengthMenu": [[8, 16, 20, -1], [8, 16, 20, "Todos"]],
        "responsive": true,
        "autoWidth": false,
        "dom": 'Bfrtip', // Agregar los botones
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-success',
                exportOptions: {
                    columns: ':not(:last-child)', // Excluir columna de acciones
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> Exportar a PDF',
                className: 'btn btn-danger',
                orientation: 'landscape', // Orientación horizontal
                pageSize: 'A4',
                title: 'Listado de Socios',
                exportOptions: {
                    columns: ':not(:last-child)', // Excluir columna de acciones
                }
            }
        ]
    });
});

function confirmarEliminar(idSocio) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción eliminará al socio de la asociación.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('modules/socio_asociacion/eliminar_asociado.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: idSocio })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('¡Eliminado!', 'El socio fue eliminado correctamente.', 'success');
                    $('#tabla-socios').DataTable().ajax.reload(null, false);
                } else {
                    Swal.fire('Error', 'No se pudo eliminar el socio.', 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Hubo un problema al conectar con el servidor.', 'error');
            });
        }
    });
}
</script>

</body>
</html>
