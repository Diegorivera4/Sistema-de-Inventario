<?php
session_start();
include '../../db/conexion.php';

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $clave = $_POST['clave'];

    if (!empty($usuario) && !empty($clave)) {
        // Verificar si ya existe
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "⚠️ El usuario ya existe.";
        } else {
            $clave_hash = password_hash($clave, PASSWORD_DEFAULT);
            $rol = 'admin'; // Fijo para administradores

            $stmt = $conexion->prepare("INSERT INTO usuarios (usuario, clave, rol) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $usuario, $clave_hash, $rol);

            if ($stmt->execute()) {
                $mensaje = "✅ Administrador registrado correctamente.";
            } else {
                $error = "❌ Error al registrar el administrador.";
            }
        }
    } else {
        $error = "❌ Todos los campos son obligatorios.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Administrador</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
  <div class="bg-white shadow p-4 rounded" style="max-width: 500px; width: 100%;">
    <h3 class="mb-4 text-center">📝 Registrar Administrador</h3>

    <?php if ($mensaje): ?>
      <div class="alert alert-success"><?= $mensaje ?></div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Usuario</label>
        <input type="text" name="usuario" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input type="password" name="clave" class="form-control" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Registrar</button>
      </div>
      <div class="text-center mt-3">
        <a href="login.php">← Volver al Login</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
