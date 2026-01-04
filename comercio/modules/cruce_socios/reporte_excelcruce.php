<?php
require_once(__DIR__ . "/../../includes/conexion.php");

// Carga Composer autoload para PhpSpreadsheet
require '../../assets/excel/vendor/autoload.php'; // Ajusta ruta si es diferente

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$idgrupo = intval($_GET['idgrupo'] ?? 0);
if ($idgrupo <= 0) {
    die("Parámetro idgrupo inválido.");
}

$limit = intval($_GET['limit'] ?? 1000);
$offset = 0;

$query = "
SELECT
    s.idsocio,
    s.dni,
    CONCAT(s.nombre, ' ', s.apellido_pat, ' ', IFNULL(s.apellido_mat, '')) AS nombre_completo,
    COUNT(DISTINCT g2.idgrupo) AS cantidad_asociaciones,
    GROUP_CONCAT(DISTINCT g2.nombre_grupo ORDER BY g2.nombre_grupo SEPARATOR '|') AS asociaciones
FROM socio s
JOIN socio_asociacion sa ON s.idsocio = sa.socio_idsocio
JOIN grupo g ON sa.grupo_idgrupo = g.idgrupo

JOIN socio_asociacion sa2 ON s.idsocio = sa2.socio_idsocio
JOIN grupo g2 ON sa2.grupo_idgrupo = g2.idgrupo

WHERE g.idgrupo = ?
AND s.idsocio IN (
    SELECT socio_idsocio
    FROM socio_asociacion sa_inner
    GROUP BY socio_idsocio
    HAVING COUNT(DISTINCT grupo_idgrupo) > 1
)
GROUP BY s.idsocio, s.dni, nombre_completo
ORDER BY nombre_completo
LIMIT ? OFFSET ?
";

$stmt = mysqli_prepare($conn, $query);
if (!$stmt) die("Error preparando query: " . mysqli_error($conn));

mysqli_stmt_bind_param($stmt, "iii", $idgrupo, $limit, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (!$result) die("Error ejecutando query: " . mysqli_error($conn));

// Obtener nombre del grupo para título
$queryGrupo = "SELECT nombre_grupo FROM grupo WHERE idgrupo = ?";
$stmtGrupo = mysqli_prepare($conn, $queryGrupo);
mysqli_stmt_bind_param($stmtGrupo, "i", $idgrupo);
mysqli_stmt_execute($stmtGrupo);
$resGrupo = mysqli_stmt_get_result($stmtGrupo);
$grupoNombre = ($rowGrupo = mysqli_fetch_assoc($resGrupo)) ? $rowGrupo['nombre_grupo'] : "Grupo";

// Crear nuevo Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Establecer título de hoja
$sheet->setTitle("Socios Multiasociación");

// Título del grupo, centrado, negrita y tamaño 14 (como el PDF)
$sheet->mergeCells('A1:E1');
$sheet->setCellValue('A1', $grupoNombre);
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Encabezados (fila 2)
$sheet->setCellValue('A2', 'Nº');
$sheet->setCellValue('B2', 'DNI');
$sheet->setCellValue('C2', 'NOMBRES Y APELLIDOS');
$sheet->setCellValue('D2', 'Cant.');
$sheet->setCellValue('E2', 'Asociaciones');

// Formato encabezados: negrita, fondo gris claro, centrado
$sheet->getStyle('A2:E2')->getFont()->setBold(true);
$sheet->getStyle('A2:E2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
      ->getStartColor()->setARGB('FFC8C8C8');
$sheet->getStyle('A2:E2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Ajustar anchos similares a los que usaste en el PDF
// En total 100%, pondré anchos relativos aproximados al PDF:
$sheet->getColumnDimension('A')->setWidth(5);   // 5%
$sheet->getColumnDimension('B')->setWidth(10);  // 10%
$sheet->getColumnDimension('C')->setWidth(30);  // 30%
$sheet->getColumnDimension('D')->setWidth(6);   // 6%
$sheet->getColumnDimension('E')->setWidth(49);  // 49%

$rowNumber = 3; // Datos empiezan en fila 3
$counter = 1;

while ($row = mysqli_fetch_assoc($result)) {
    $asociacionesArray = explode('|', $row['asociaciones']);

    $first = true;
    foreach ($asociacionesArray as $asoc) {
        if ($first) {
            // Primera fila con datos completos
            $sheet->setCellValue("A{$rowNumber}", $counter);
            $sheet->setCellValue("B{$rowNumber}", $row['dni']);
            $sheet->setCellValue("C{$rowNumber}", $row['nombre_completo']);
            $sheet->setCellValue("D{$rowNumber}", $row['cantidad_asociaciones']);
            $sheet->setCellValue("E{$rowNumber}", $asoc);

            // Alineaciones
            $sheet->getStyle("A{$rowNumber}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$rowNumber}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$rowNumber}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("D{$rowNumber}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$rowNumber}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $first = false;
        } else {
            // Filas adicionales solo con la asociación, otras columnas vacías
            $sheet->setCellValue("E{$rowNumber}", $asoc);
            // Izquierda para asociación
            $sheet->getStyle("E{$rowNumber}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }
        $rowNumber++;
    }
    $counter++;
}

// Forzar descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Socios_Multiasociacion_Grupo_'.$idgrupo.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
