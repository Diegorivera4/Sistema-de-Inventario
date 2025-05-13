<?php
require('fpdf/fpdf.php');
include '../../db/conexion.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(0, 10, 'Inventario de Productos', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 10, 'ID', 1);
$pdf->Cell(40, 10, 'Nombre', 1);
$pdf->Cell(50, 10, 'Descripcion', 1);
$pdf->Cell(30, 10, 'Categoria', 1);
$pdf->Cell(20, 10, 'Precio', 1);
$pdf->Cell(20, 10, 'Stock', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$resultado = $conexion->query("SELECT * FROM productos");

while ($fila = $resultado->fetch_assoc()) {
    $pdf->Cell(10, 8, $fila['id'], 1);
    $pdf->Cell(40, 8, utf8_decode($fila['nombre']), 1);
    $pdf->Cell(50, 8, utf8_decode(substr($fila['descripcion'], 0, 30)), 1);
    $pdf->Cell(30, 8, utf8_decode($fila['categoria']), 1);
    $pdf->Cell(20, 8, $fila['precio'], 1);
    $pdf->Cell(20, 8, $fila['stock'], 1);
    $pdf->Ln();
}

$pdf->Output();
?>
