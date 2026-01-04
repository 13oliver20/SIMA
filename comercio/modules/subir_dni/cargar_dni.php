<?php

require __DIR__ . '/../../assets/excel/vendor/autoload.php';
require __DIR__ . '/../../includes/conexion.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No se recibió un archivo válido.']);
    exit;
}

$tmpName = $_FILES['archivo']['tmp_name'];

try {
    $spreadsheet = IOFactory::load($tmpName);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al leer el archivo Excel: ' . $e->getMessage()]);
    exit;
}

$hoja = $spreadsheet->getActiveSheet();
$filas = $hoja->toArray();

$totalDNIs = 0;
$validos = 0;
$invalidos = 0;

// Contar repeticiones
$dniContador = [];
foreach ($filas as $i => $fila) {
    if ($i === 0) continue;
    $dniRaw = trim($fila[0]);
    $dni = str_pad($dniRaw, 8, '0', STR_PAD_LEFT);
    $dniContador[$dni] = isset($dniContador[$dni]) ? $dniContador[$dni] + 1 : 1;
}

$resultado = [];
$resultado[] = ['DNI', 'Nombre Completo', 'Grupo', 'Estado', 'Observación'];

foreach ($filas as $i => $fila) {
    if ($i === 0) continue;

    $totalDNIs++;

    $dniRaw = trim($fila[0]);
    $dni = str_pad($dniRaw, 8, '0', STR_PAD_LEFT);
    $observacion = '';

    // Verificar si es "00000000" o vacío
    if ($dni === '00000000') {
        $invalidos++;
        $resultado[] = [$dniRaw, '-', '-', 'Inválido', 'DNI vacío o 00000000'];
        continue;
    }

    // Verificar si es válido en formato
    if (!preg_match('/^\d{8}$/', $dni)) {
        $invalidos++;
        $resultado[] = [$dniRaw, '-', '-', 'Inválido', 'Formato incorrecto'];
        continue;
    }

    if ($dniContador[$dni] > 1) {
        $observacion = 'DNI repetido en archivo';
    }

    $validos++;

    $stmt = $conn->prepare("SELECT idsocio, nombre, apellido_pat, apellido_mat FROM socio WHERE dni = ?");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $socio = $res->fetch_assoc();
        $idsocio = $socio['idsocio'];
        $nombreCompleto = trim("{$socio['nombre']} {$socio['apellido_pat']} {$socio['apellido_mat']}");

        $stmt2 = $conn->prepare("
            SELECT g.nombre_grupo 
            FROM socio_asociacion sa
            JOIN grupo g ON g.idgrupo = sa.grupo_idgrupo
            WHERE sa.socio_idsocio = ?
        ");
        $stmt2->bind_param("i", $idsocio);
        $stmt2->execute();
        $resGrupo = $stmt2->get_result();

        if ($resGrupo->num_rows > 0) {
            while ($grupo = $resGrupo->fetch_assoc()) {
                $resultado[] = [$dni, $nombreCompleto, $grupo['nombre_grupo'], 'Registrado', $observacion];
            }
        } else {
            $resultado[] = [$dni, $nombreCompleto, '-', 'No asociado a grupo', $observacion];
        }
        $stmt2->close();
    } else {
        $resultado[] = [$dni, '-', '-', 'No registrado', $observacion];
    }

    $stmt->close();
}

// Crear Excel de resultados
$spreadsheetResult = new Spreadsheet();
$sheet = $spreadsheetResult->getActiveSheet();

$titulo = "REPORTE DE REGISTRO EN NUESTRA BASE DE DATOS SIMA - FECHA: " . date('Y-m-d');
$sheet->setCellValue('A1', $titulo);
$sheet->mergeCells('A1:E1');

$sheet->getStyle('A1')->applyFromArray([
    'font' => ['bold' => true, 'size' => 14],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
]);

$sheet->fromArray($resultado, NULL, 'A3');

$sheet->getStyle('A3:E3')->applyFromArray([
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
]);

foreach (range('A', 'E') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$pageSetup = $sheet->getPageSetup();
$pageSetup->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
$pageSetup->setFitToPage(true);
$pageSetup->setFitToWidth(1);
$pageSetup->setFitToHeight(0);
$pageSetup->setHorizontalCentered(true);

$dirTemp = __DIR__ . '/temp/';
if (!file_exists($dirTemp)) {
    mkdir($dirTemp, 0777, true);
}

$nombreArchivo = "resultado_dnis_" . date('Ymd_His') . ".xlsx";
$rutaArchivo = $dirTemp . $nombreArchivo;

$writer = new Xlsx($spreadsheetResult);

try {
    $writer->save($rutaArchivo);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al guardar el archivo: ' . $e->getMessage()]);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Archivo procesado correctamente.',
    'ruta' => "modules/subir_dni/temp/" . $nombreArchivo,
    'total' => $totalDNIs,
    'validos' => $validos,
    'invalidos' => $invalidos
]);
exit;
