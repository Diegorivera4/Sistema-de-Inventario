<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: public/admin/login.php");
    exit();
}

include '../db/conexion.php';

// Paginación
$por_pagina = 10;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina > 1) ? ($pagina * $por_pagina - $por_pagina) : 0;

// Búsqueda
$condicion = '';
$busqueda = '';

if (!empty($_GET['busqueda'])) {
    $busqueda = $conexion->real_escape_string($_GET['busqueda']);
    $condicion = "WHERE nombre LIKE '%$busqueda%' OR categoria LIKE '%$busqueda%'";
}

// Total
$total_sql = "SELECT COUNT(*) as total FROM productos $condicion";
$total_resultado = $conexion->query($total_sql);
$total_fila = $total_resultado->fetch_assoc();
$total_productos = $total_fila['total'];
$total_paginas = ceil($total_productos / $por_pagina);

// Consulta
$sql = "SELECT * FROM productos $condicion LIMIT $inicio, $por_pagina";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inventario de Productos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container py-4 animate-fade-in">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
      <img src="https://cdn-icons-png.flaticon.com/512/891/891462.png" width="40">
      Inventario de Productos
    </h2>
    <div class="btn-group">
    <a href="admin/logout.php" class="btn btn-outline-danger btn-sm">🔓 Cerrar sesión</a>
      <a href="historial/index.php" class="btn btn-outline-secondary btn-sm">📜 Historial</a>
      <a href="dashboard.php" class="btn btn-outline-primary btn-sm">📈 Estadísticas</a>
      <button class="btn btn-outline-dark btn-sm" id="toggle-theme">🌓 Modo Oscuro</button>
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <form method="GET" class="d-flex" role="search">
      <input class="form-control me-2" type="text" name="busqueda" placeholder="Buscar por nombre o categoría..." value="<?= htmlspecialchars($busqueda) ?>">
      <button class="btn btn-outline-primary" type="submit">Buscar</button>
    </form>
    <div>
      <a href="agregar.php" class="btn btn-success">➕ Agregar producto</a>
    </div>
  </div>

  <div class="mb-3">
    <a href="exportar/exportar_excel.php" class="btn btn-outline-success btn-sm" target="_blank">📥 Exportar a Excel</a>
    <a href="exportar/exportar_pdf.php" class="btn btn-outline-danger btn-sm" target="_blank">📄 Exportar a PDF</a>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Categoría</th>
          <th>Precio</th>
          <th>Stock</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($fila = $resultado->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($fila['nombre']) ?></td>
          <td><?= htmlspecialchars($fila['descripcion']) ?></td>
          <td><?= htmlspecialchars($fila['categoria']) ?></td>
          <td>$<?= number_format($fila['precio'], 2) ?></td>
          <td><?= $fila['stock'] ?></td>
          <td>
            <span class="badge <?= $fila['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
              <?= $fila['stock'] > 0 ? 'Disponible' : 'Agotado' ?>
            </span>
          </td>
          <td>
            <a href="editar.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-warning">✏️ Editar</a>
            <button class="btn btn-sm btn-danger btn-eliminar" data-id="<?= $fila['id'] ?>">🗑️ Eliminar</button>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <nav>
    <ul class="pagination justify-content-center">
      <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
      <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
        <a class="page-link" href="?pagina=<?= $i ?>&busqueda=<?= urlencode($busqueda) ?>"><?= $i ?></a>
      </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.querySelectorAll('.btn-eliminar').forEach(button => {
    button.addEventListener('click', function () {
      const id = this.getAttribute('data-id');
      Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = `eliminar.php?id=${id}`;
        }
      });
    });
  });

  // Modo oscuro
  const toggleTheme = document.getElementById('toggle-theme');
  const currentTheme = localStorage.getItem('theme') || 'light';
  document.body.classList.add(currentTheme);
  toggleTheme.textContent = currentTheme === 'dark' ? '🌞 Modo Claro' : '🌓 Modo Oscuro';
  toggleTheme.addEventListener('click', () => {
    const isDark = document.body.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    toggleTheme.textContent = isDark ? '🌞 Modo Claro' : '🌓 Modo Oscuro';
  });
</script>
</body>
</html>
