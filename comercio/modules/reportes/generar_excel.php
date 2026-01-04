<?php
require '../../assets/excel/vendor/autoload.php'; // Ajusta ruta si es diferente

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Conexión y consulta
require_once(__DIR__ . '/../../includes/conexion.php');
mysqli_set_charset($conn, "utf8");

$idgrupo = intval($_GET['idgrupo'] ?? 0);
if ($idgrupo <= 0) {
    die("ID de grupo inválido");
}

// Obtener nombre del grupo
$sqlGrupo = "SELECT nombre_grupo FROM grupo WHERE idgrupo = $idgrupo LIMIT 1";
$resGrupo = mysqli_query($conn, $sqlGrupo);
if (!$resGrupo || mysqli_num_rows($resGrupo) == 0) {
    die("Grupo no encontrado");
}
$rowGrupo = mysqli_fetch_assoc($resGrupo);
$nombreGrupo = $rowGrupo['nombre_grupo'];

// Obtener datos de socios asociados al grupo
$sql = "
SELECT 
    s.nombre, s.apellido_pat, s.apellido_mat, s.dni,
    CONCAT(rp.nombre, ' - ', sp.nombre) AS rubro_completo
FROM socio s
INNER JOIN socio_asociacion sa ON s.idsocio = sa.socio_idsocio
LEFT JOIN subrubro_segundo ss ON ss.idsubrubro_seg = sa.subrubro_segundo_idsubrubro_seg
LEFT JOIN subrubro_primero sp ON sp.idsubrubro = ss.subrubro_primero_idsubrubro
LEFT JOIN rubro_principal rp ON rp.idrubro = sp.rubro_principal_idrubro
WHERE sa.grupo_idgrupo = $idgrupo
ORDER BY s.apellido_pat, s.apellido_mat, s.nombre
";

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error en consulta: " . mysqli_error($conn));
}

// Crear spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Fila 1: título
$sheet->setCellValue('A1', 'Padrón del Grupo: ' . $nombreGrupo);
$sheet->mergeCells('A1:E1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

// Fila 2: vacía (no se necesita código adicional)

// Fila 3: encabezados
$sheet->setCellValue('A3', 'Nº');
$sheet->setCellValue('B3', 'DNI');
$sheet->setCellValue('C3', 'Apellidos');
$sheet->setCellValue('D3', 'Nombres');
$sheet->setCellValue('E3', 'Rubro');

// Estilo de encabezados
$sheet->getStyle('A3:E3')->getFont()->setBold(true);

// Rellenar filas a partir de la fila 4
$rowNum = 4;
$contador = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $apellidos = $row['apellido_pat'] . ' ' . $row['apellido_mat'];
    $sheet->setCellValue('A' . $rowNum, $contador++);
    $sheet->setCellValue('B' . $rowNum, $row['dni']);
    $sheet->setCellValue('C' . $rowNum, $apellidos);
    $sheet->setCellValue('D' . $rowNum, $row['nombre']);
    $sheet->setCellValue('E' . $rowNum, $row['rubro_completo'] ?? '---');
    $rowNum++;
}

// Autoancho de columnas
foreach (range('A', 'E') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Descargar Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Padron_Grupo_' . $idgrupo . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
