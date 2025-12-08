<?php
session_start();

// Limpieza de buffer para evitar errores de FPDF
ob_start();

// 1. Incluir librería y CAD
require('../libs/fpdf186/fpdf.php');
require_once '../bd/cad.php';

if (!isset($_SESSION['id_usuario'])) { die("Acceso denegado"); }

// 2. DETECTAR PARAMETROS (Inteligencia de Reporte)
// 'tipo' viene del botón rápido, 'reportType' viene del formulario manual. Usamos cualquiera.
$tipo = $_GET['reportType'] ?? $_GET['tipo'] ?? 'mensual';
$categoria = $_GET['category'] ?? 'todas';

$tituloPeriodo = "";
$valorTiempo = "";

// Lógica para determinar el periodo de tiempo
if ($tipo === 'semanal') {
    // CASO 1: MANUAL (El usuario eligió una semana específica en el input)
    if (isset($_GET['reportWeek']) && !empty($_GET['reportWeek'])) {
        $valorTiempo = $_GET['reportWeek']; // Formato: "2025-W42"
    } 
    // CASO 2: REPORTE RÁPIDO (Botón del dashboard -> Última semana completa)
    else {
        // 'o' es el año ISO, 'W' es la semana ISO. strtotime('last week') nos da la semana anterior completa.
        $valorTiempo = date('o-\WW', strtotime('last week')); 
    }
} 
else { // Mensual
    // CASO 1: MANUAL (El usuario eligió un mes específico)
    if (isset($_GET['reportMonth']) && !empty($_GET['reportMonth'])) {
        $valorTiempo = $_GET['reportMonth']; // Formato: "2025-10"
    } 
    // CASO 2: REPORTE RÁPIDO (Botón del dashboard -> Último mes completo)
    else {
        // 'first day of last month' asegura que si hoy es 31 de marzo, nos de Febrero correctamente.
        $valorTiempo = date('Y-m', strtotime('first day of last month'));
    }
}

$tituloPeriodo = $valorTiempo;

// 3. Obtener datos de la BD
$datos = CAD::getReporte($tipo, $valorTiempo, $categoria);

ob_end_clean(); // Limpiar basura antes del PDF

// 4. GENERAR PDF
class PDF extends FPDF {
    function Header() {
        // Logo (Descomenta y ajusta ruta si lo usas)
        // $this->Image('../multimedia/Logo_Inge.jpg',10,8,15);
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10, utf8_decode('Sistema de Control de Oficinas'),0,1,'C');
        $this->SetFont('Arial','',10);
        $this->Cell(0,5, utf8_decode('Reporte de Préstamos'),0,1,'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb} - Generado el: '.date('d/m/Y H:i'),0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',11);
$pdf->SetTitle(utf8_decode("ReporteSCO_$tituloPeriodo"));

// Información del Reporte
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 8, utf8_decode("Periodo: $tituloPeriodo"), 0, 1, 'L', true);
$pdf->Cell(0, 8, utf8_decode("Categoría: " . ucfirst($categoria)), 0, 1, 'L', true);
$pdf->Cell(0, 8, utf8_decode("Total Registros: " . count($datos)), 0, 1, 'L', true);
$pdf->Ln(5);

// Tabla
$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(28, 57, 142); // Azul corporativo
$pdf->SetTextColor(255, 255, 255); // Blanco

$pdf->Cell(15, 8, 'ID', 1, 0, 'C', true);
$pdf->Cell(60, 8, utf8_decode('Artículo'), 1, 0, 'C', true);
$pdf->Cell(50, 8, 'Estudiante', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Fecha', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Estado', 1, 1, 'C', true);

$pdf->SetTextColor(0, 0, 0); // Negro
$pdf->SetFont('Arial','',8);

foreach ($datos as $fila) {
    $pdf->Cell(15, 7, $fila['id_prestamo'], 1, 0, 'C');
    $pdf->Cell(60, 7, substr(utf8_decode($fila['articulo']), 0, 35), 1);
    $pdf->Cell(50, 7, substr(utf8_decode($fila['estudiante']), 0, 28), 1);
    $pdf->Cell(35, 7, $fila['fecha_prestamo'], 1, 0, 'C');
    $pdf->Cell(30, 7, ucfirst($fila['estado']), 1, 1, 'C');
}

if (count($datos) == 0) {
    $pdf->Ln(5);
    $pdf->SetFont('Arial','I',10);
    $pdf->Cell(0, 10, utf8_decode('No se encontraron registros para este periodo.'), 0, 1, 'C');
}

$pdf->Output('I', 'Reporte_Prestamos.pdf'); // 'I' para ver en navegador
?>