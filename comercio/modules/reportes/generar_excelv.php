<?php
require '../../assets/excel/vendor/autoload.php'; // Ajusta ruta si es diferente

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Conexión y consulta (ejemplo simple)
require_once(__DIR__ . '/../../includes/conexion.php');
mysqli_set_charset($conn, "utf8");

// Validar ID
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

// Consulta de datos
$sql = "
SELECT 
    s.nombre, s.apellido_pat, s.apellido_mat, s.dni,
    CONCAT(rp.nombre, ' - ', sp.nombre) AS rubro_completo,
    CASE 
        WHEN COUNT(CASE WHEN va.verificacion = 'Presente' THEN 1 END) > 0 THEN 'ACTIVO'
        WHEN COUNT(CASE WHEN va.verificacion = 'Justificado' THEN 1 END) > 0 THEN 'ACTIVO'
        WHEN COUNT(va.verificacion) = 0 THEN '-'
        ELSE 'INACTIVO'
    END AS estado
FROM socio s
INNER JOIN socio_asociacion sa ON s.idsocio = sa.socio_idsocio
LEFT JOIN verificacion_asociados va 
    ON va.socio_asociacion_socio_idsocio = sa.socio_idsocio 
    AND va.socio_asociacion_grupo_idgrupo = sa.grupo_idgrupo
LEFT JOIN subrubro_segundo ss ON ss.idsubrubro_seg = sa.subrubro_segundo_idsubrubro_seg
LEFT JOIN subrubro_primero sp ON sp.idsubrubro = ss.subrubro_primero_idsubrubro
LEFT JOIN rubro_principal rp ON rp.idrubro = sp.rubro_principal_idrubro
WHERE sa.grupo_idgrupo = $idgrupo
GROUP BY s.idsocio
ORDER BY s.apellido_pat, s.apellido_mat, s.nombre
";

$result = mysqli_query($conn, $sql);

// Crear hoja de Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Título
$sheet->setCellValue('A1', 'Padrón del Grupo: ' . $nombreGrupo);
$sheet->mergeCells('A1:F1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

// Cabeceras
$headers = ['Nº', 'DNI', 'APELLIDOS', 'NOMBRES', 'RUBRO', 'ESTADO'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '3', $header);
    $sheet->getStyle($col . '3')->getFont()->setBold(true);
    $sheet->getColumnDimension($col)->setAutoSize(true);
    $col++;
}

// Datos
$rowIndex = 4;
$contador = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $apellidos = $row['apellido_pat'] . ' ' . $row['apellido_mat'];
    $sheet->setCellValue('A' . $rowIndex, $contador++);
    $sheet->setCellValue('B' . $rowIndex, $row['dni']);
    $sheet->setCellValue('C' . $rowIndex, $apellidos);
    $sheet->setCellValue('D' . $rowIndex, $row['nombre']);
    $sheet->setCellValue('E' . $rowIndex, $row['rubro_completo'] ?? '---');
    $sheet->setCellValue('F' . $rowIndex, $row['estado']);
    $rowIndex++;
}

// Descargar el archivo Excel
$filename = 'Padron_Grupo_' . $idgrupo . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
