<?php
session_start();
include '../db/conexion.php';

$id = $_GET['id'];

// Obtener el nombre antes de eliminar
$consulta = $conexion->query("SELECT nombre FROM productos WHERE id = $id");
$fila = $consulta->fetch_assoc();
$nombre = $fila['nombre'];

// Registrar en historial
$descripcion = "Producto eliminado: $nombre";
$conexion->query("INSERT INTO historial (producto_id, usuario, tipo_movimiento, descripcion) 
                  VALUES ($id, '{$_SESSION['admin']}', 'ELIMINADO', '$descripcion')");

// Eliminar producto
$conexion->query("DELETE FROM productos WHERE id = $id");

header("Location: index.php");
