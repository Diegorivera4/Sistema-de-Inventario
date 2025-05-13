<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../admin/login.php");
    exit();
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=historial_movimientos.xls");

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

$historial = $conexion->query("
  SELECT h.*, p.nombre AS producto 
  FROM historial h 
  LEFT JOIN productos p ON h.producto_id = p.id 
  $condicion
  ORDER BY h.fecha DESC
");

echo "<table border='1'>";
echo "<tr>
        <th>Fecha</th>
        <th>Producto</th>
        <th>Movimiento</th>
        <th>Usuario</th>
        <th>Descripción</th>
      </tr>";

while ($fila = $historial->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . date("d/m/Y H:i", strtotime($fila['fecha'])) . "</td>";
    echo "<td>" . htmlspecialchars($fila['producto']) . "</td>";
    echo "<td>" . $fila['tipo_movimiento'] . "</td>";
    echo "<td>" . htmlspecialchars($fila['usuario']) . "</td>";
    echo "<td>" . htmlspecialchars($fila['descripcion']) . "</td>";
    echo "</tr>";
}

echo "</table>";
?>
