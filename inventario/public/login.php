<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  include('../../db/conexion.php');

    $usuario = trim($_POST['usuario']);
    $clave = $_POST['clave'];

    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $datos = $resultado->fetch_assoc();

        if (password_verify($clave, $datos['clave'])) {
            if ($datos['rol'] === 'admin') {
                $_SESSION['usuario'] = $datos['usuario'];
                $_SESSION['rol'] = $datos['rol'];
                $_SESSION['nombre'] = $datos['nombre'];

                header("Location: ../index.php");
                exit();
            } else {
                $error = "❌ Solo los administradores pueden ingresar.";
            }
        } else {
            $error = "❌ Contraseña incorrecta.";
        }
    } else {
        $error = "❌ Usuario no encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login Administrador</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="bg-white shadow p-4 rounded w-100" style="max-width: 400px;">
      <h2 class="text-center mb-4">🔐 Login Administrador</h2>

      <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
      <?php endif; ?>

      <form method="POST" autocomplete="off">
        <div class="mb-3">
          <label for="usuario" class="form-label">Usuario</label>
          <input type="text" name="usuario" id="usuario" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="clave" class="form-label">Contraseña</label>
          <input type="password" name="clave" id="clave" class="form-control" required>
        </div>

        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Ingresar</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
