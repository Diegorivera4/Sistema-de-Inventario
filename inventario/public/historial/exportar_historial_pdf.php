<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../admin/login.php");
    exit();
}

require('fpdf/fpdf.php');
include '../../db/conexion.php';

$tipo = $_GET['tipo'] ?? '';
$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';

$where = [];

if ($tipo !== '') {
    $where[] = "h.tipo_movimiento = '$tipo'";
}
if ($desde !== '') {
    $where[] = "DATE(h.fecha) >= '$desde'";
}
if ($hasta !== '') {
    $where[] = "DATE(h.fecha) <= '$hasta'";
}

$condicion = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

$consulta = $conexion->query("
  SELECT h.*, p.nombre AS producto 
  FROM historial h 
  LEFT JOIN productos p ON h.producto_id = p.id 
  $condicion
  ORDER BY h.fecha DESC
");

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'📋 Historial de Movimientos',0,1,'C');
        $this->Ln(5);
        $this->SetFont('Arial','B',10);
        $this->SetFillColor(200,200,200);
        $this->Cell(35,8,'Fecha',1,0,'C',true);
        $this->Cell(45,8,'Producto',1,0,'C',true);
        $this->Cell(30,8,'Movimiento',1,0,'C',true);
        $this->Cell(30,8,'Usuario',1,0,'C',true);
        $this->Cell(50,8,'Descripcion',1,1,'C',true);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina '.$this->PageNo(),0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);

while($row = $consulta->fetch_assoc()) {
    $pdf->Cell(35,8,date("d/m/Y H:i", strtotime($row['fecha'])),1);
    $pdf->Cell(45,8,utf8_decode($row['producto']),1);
    $pdf->Cell(30,8,$row['tipo_movimiento'],1);
    $pdf->Cell(30,8,utf8_decode($row['usuario']),1);
    $pdf->Cell(50,8,utf8_decode($row['descripcion']),1);
    $pdf->Ln();
}

$pdf->Output('I', 'historial_movimientos.pdf');
?>
