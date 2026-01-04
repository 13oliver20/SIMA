<?php
require_once(__DIR__ . '/../../includes/conexion.php');
require(__DIR__ . '/../../assets/fpdf/fpdf.php');

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

// Consulta de socios y rubros (sin estado)
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
GROUP BY s.idsocio
ORDER BY s.apellido_pat, s.apellido_mat, s.nombre
";
$result = mysqli_query($conn, $sql);

// Clase PDF personalizada
class PDF extends FPDF {
    public $nombreGrupo;
    protected $widths;

    function SetWidths($w) {
        $this->widths = $w;
    }

    function Header() {
        if ($this->PageNo() == 1) {
            $this->SetFont('Arial', 'B', 14);
            $this->SetX($this->lMargin);
            $this->MultiCell(0, 10, utf8_decode($this->nombreGrupo), 0, 'C');
            $this->Ln(2);
        }

        $totalWidth = $this->GetPageWidth() - $this->lMargin - $this->rMargin;
        $this->SetWidths([
            $totalWidth * 0.05, // Nº
            $totalWidth * 0.10, // DNI
            $totalWidth * 0.32, // APELLIDOS
            $totalWidth * 0.23, // NOMBRES
            $totalWidth * 0.30  // RUBRO
        ]);

        $this->SetFont('Arial', 'B', 10);
        $this->Cell($this->widths[0], 8, utf8_decode('Nº'), 1, 0, 'C');
        $this->Cell($this->widths[1], 8, utf8_decode('DNI'), 1, 0, 'C');
        $this->Cell($this->widths[2], 8, utf8_decode('APELLIDOS'), 1, 0, 'C');
        $this->Cell($this->widths[3], 8, utf8_decode('NOMBRES'), 1, 0, 'C');
        $this->Cell($this->widths[4], 8, utf8_decode('RUBRO'), 1, 0, 'C');
        $this->Ln();
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo()), 0, 0, 'C');
    }

    function Row($data) {
        $this->SetFont('Arial', '', 8);
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }
        $h = 5 * $nb;
        $this->CheckPageBreak($h);
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = ($i == 0 || $i == 1) ? 'C' : 'L';
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h);
            $this->MultiCell($w, 5, utf8_decode($data[$i]), 0, $a);
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h);
    }

    function CheckPageBreak($h) {
        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
        }
    }

    function NbLines($w, $txt) {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") $nb--;
        $sep = -1;
        $i = 0; $j = 0; $l = 0; $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++; $sep = -1; $j = $i; $l = 0; $nl++; continue;
            }
            if ($c == ' ') $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) $i++;
                } else {
                    $i = $sep + 1;
                }
                $sep = -1; $j = $i; $l = 0; $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }
}

// Crear PDF
$pdf = new PDF();
$pdf->nombreGrupo = $nombreGrupo;
$pdf->AddPage();

// Llenar filas
$contador = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $apellidos = $row['apellido_pat'] . ' ' . $row['apellido_mat'];
    $pdf->Row([
        $contador++,
        $row['dni'],
        $apellidos,
        $row['nombre'],
        $row['rubro_completo'] ?? '---'
    ]);
}

$pdf->Output('I', 'Padron_Grupo_' . $idgrupo . '.pdf');
?>
