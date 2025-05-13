<?php
// Configuración de conexión
$host = 'localhost';
$puerto = 3307; // Puerto MySQL personalizado
$usuario = 'root';
$contrasena = '';
$base_datos = 'inventario';

// Crear conexión con puerto especificado
$conexion = new mysqli($host, $usuario, $contrasena, $base_datos, $puerto);

// Verificar errores de conexión
if ($conexion->connect_error) {
    die("❌ Error de conexión: " . $conexion->connect_error);
}

// Establecer codificación de caracteres a UTF-8
if (!$conexion->set_charset("utf8mb4")) {
    die("❌ Error al establecer el charset utf8mb4: " . $conexion->error);
}
?>
