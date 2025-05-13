<?php
include '../../db/conexion.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=inventario.xls");

$resultado = $conexion->query("SELECT * FROM productos");

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Categoría</th><th>Precio</th><th>Stock</th></tr>";

while ($fila = $resultado->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$fila['id']}</td>";
    echo "<td>{$fila['nombre']}</td>";
    echo "<td>{$fila['descripcion']}</td>";
    echo "<td>{$fila['categoria']}</td>";
    echo "<td>{$fila['precio']}</td>";
    echo "<td>{$fila['stock']}</td>";
    echo "</tr>";
}
echo "</table>";
?>
