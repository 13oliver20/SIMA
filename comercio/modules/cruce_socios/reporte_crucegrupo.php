<?php
require_once(__DIR__ . "/../../includes/conexion.php");
require(__DIR__ . "/../../assets/fpdf/fpdf.php");

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

$queryGrupo = "SELECT nombre_grupo FROM grupo WHERE idgrupo = ?";
$stmtGrupo = mysqli_prepare($conn, $queryGrupo);
if (!$stmtGrupo) die("Error preparando queryGrupo: " . mysqli_error($conn));
mysqli_stmt_bind_param($stmtGrupo, "i", $idgrupo);
mysqli_stmt_execute($stmtGrupo);
$resGrupo = mysqli_stmt_get_result($stmtGrupo);
if (!$resGrupo) die("Error ejecutando queryGrupo: " . mysqli_error($conn));
$grupoNombre = ($rowGrupo = mysqli_fetch_assoc($resGrupo)) ? $rowGrupo['nombre_grupo'] : "Grupo";

class PDF extends FPDF {
    var $widths;
    var $aligns;

    private $grupoNombre;
    function __construct($grupoNombre) {
        parent::__construct('P');
        $this->grupoNombre = $grupoNombre;
    }

    function SetWidths($w) {
        // Establecer anchos de columnas
        $this->widths = $w;
    }

    function SetAligns($a) {
        // Establecer alineaciones de columnas
        $this->aligns = $a;
    }

    function Row($data) {
        // Calcular la altura máxima de la fila
        $nb = 0;
        for ($i=0; $i < count($data); $i++) {
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }
        $h = 6 * $nb;
        // Verificar salto de página
        $this->CheckPageBreak($h);
        // Dibujar celdas
        for ($i=0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h);
            $this->MultiCell($w, 6, utf8_decode($data[$i]), 0, $a);
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h);
    }

    function CheckPageBreak($h) {
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt) {
        // Calcular cantidad de líneas de MultiCell
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

    function Header() {
    $this->SetFont('Arial','B',14);
    $w = $this->GetPageWidth() - $this->lMargin - $this->rMargin;
    $this->MultiCell($w, 7, utf8_decode($this->grupoNombre), 0, 'C');
    $this->Ln(2);

    $totalWidth = $this->GetPageWidth() - $this->lMargin - $this->rMargin;
    $this->SetWidths([
        $totalWidth * 0.05,  // Nº
        $totalWidth * 0.09,  // DNI
        $totalWidth * 0.30,  // Nombre Completo
        $totalWidth * 0.06,  // Cantidad
        $totalWidth * 0.50   // Asociaciones
    ]);
    $this->SetAligns(['C', 'C', 'L', 'C', 'L']);

    $this->SetFont('Arial','B',10);
    $this->SetFillColor(200,200,200);
    $this->Row(['Nº', 'DNI', 'NOMBRES Y APELLIDOS', 'Cant.', 'ASOCIACIONES']);
}

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,utf8_decode('Página ').$this->PageNo(),0,0,'C');
    }
}

$pdf = new PDF($grupoNombre);
$pdf->AddPage();
$pdf->SetFont('Arial','',8);

$counter = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $asociaciones = explode('|', $row['asociaciones']);
    $first = true;
    foreach ($asociaciones as $asoc) {
        if ($first) {
            $pdf->Row([
                $counter,
                $row['dni'],
                $row['nombre_completo'],
                $row['cantidad_asociaciones'],
                $asoc
            ]);
            $first = false;
        } else {
            $pdf->Row(['', '', '', '', $asoc]);
        }
    }
    $counter++;
}

$pdf->Output('I', "Socios_Multiasociacion_Grupo_$idgrupo.pdf");
