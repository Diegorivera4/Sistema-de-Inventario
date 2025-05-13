<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: admin/login.php");
    exit();
}

include '../db/conexion.php';
$errores = [];

$id = $_GET['id'];
$resultado = $conexion->query("SELECT * FROM productos WHERE id = $id");
$producto = $resultado->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $categoria = trim($_POST['categoria']);

    if ($nombre === '') $errores[] = "El nombre es obligatorio.";
    if (!is_numeric($precio) || $precio < 0) $errores[] = "El precio debe ser un número válido.";
    if (!is_numeric($stock) || $stock < 0) $errores[] = "El stock debe ser un número válido.";

    if (empty($errores)) {
        $stmt = $conexion->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, categoria=? WHERE id=?");
        $stmt->bind_param("ssdisi", $nombre, $descripcion, $precio, $stock, $categoria, $id);
        $stmt->execute();

        // Registrar en historial
        $descripcion_historial = "Producto editado: $nombre";
        $conexion->query("INSERT INTO historial (producto_id, usuario, tipo_movimiento, descripcion) 
                          VALUES ($id, '{$_SESSION['admin']}', 'EDITADO', '$descripcion_historial')");

        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Producto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h1 class="mb-4 text-center">✏️ Editar Producto</h1>

  <div class="mb-3 text-end">
    <a href="index.php" class="btn btn-secondary">← Volver al inventario</a>
  </div>

  <?php if (!empty($errores)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errores as $error): ?>
          <li><?= $error ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" action="" class="bg-white p-4 shadow rounded" onsubmit="return validarFormulario()">
    <div class="mb-3">
      <label for="nombre" class="form-label">Nombre del producto</label>
      <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
    </div>

    <div class="mb-3">
      <label for="descripcion" class="form-label">Descripción</label>
      <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?= htmlspecialchars($producto['descripcion']) ?></textarea>
    </div>

    <div class="mb-3">
      <label for="categoria" class="form-label">Categoría</label>
      <input type="text" class="form-control" id="categoria" name="categoria" value="<?= htmlspecialchars($producto['categoria']) ?>">
    </div>

    <div class="mb-3">
      <label for="precio" class="form-label">Precio</label>
      <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="<?= $producto['precio'] ?>" required>
    </div>

    <div class="mb-3">
      <label for="stock" class="form-label">Stock</label>
      <input type="number" class="form-control" id="stock" name="stock" value="<?= $producto['stock'] ?>" required>
    </div>

    <div class="d-grid">
      <button type="submit" class="btn btn-warning">Actualizar Producto</button>
    </div>
  </form>
</div>

<script>
function validarFormulario() {
  const nombre = document.getElementById('nombre').value.trim();
  const precio = document.getElementById('precio').value;
  const stock = document.getElementById('stock').value;

  if (nombre === '') {
    alert("El nombre es obligatorio.");
    return false;
  }
  if (isNaN(precio) || precio < 0) {
    alert("El precio debe ser un número válido.");
    return false;
  }
  if (isNaN(stock) || stock < 0) {
    alert("El stock debe ser un número válido.");
    return false;
  }
  return true;
}
</script>
</body>
</html>
