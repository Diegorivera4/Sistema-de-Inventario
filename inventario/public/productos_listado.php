<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: admin/login.php");
    exit();
}

include '../db/conexion.php';

// Totales
$total = $conexion->query("SELECT COUNT(*) AS total FROM productos")->fetch_assoc()['total'];
$disponibles = $conexion->query("SELECT COUNT(*) AS total FROM productos WHERE stock > 0")->fetch_assoc()['total'];
$agotados = $conexion->query("SELECT COUNT(*) AS total FROM productos WHERE stock = 0")->fetch_assoc()['total'];

// Categoría más usada
$cat = $conexion->query("SELECT categoria, COUNT(*) AS cantidad FROM productos GROUP BY categoria ORDER BY cantidad DESC LIMIT 1");
$categoria_popular = ($fila = $cat->fetch_assoc()) ? $fila['categoria'] : 'N/A';

// Producto más reciente
$reciente = $conexion->query("SELECT nombre FROM productos ORDER BY id DESC LIMIT 1");
$producto_reciente = ($fila = $reciente->fetch_assoc()) ? $fila['nombre'] : 'N/A';

// Gráfico 1: cantidad por categoría
$categoria_query = $conexion->query("SELECT categoria, COUNT(*) AS cantidad FROM productos GROUP BY categoria");
$categorias = [];
$valores = [];
while ($row = $categoria_query->fetch_assoc()) {
    $categorias[] = $row['categoria'];
    $valores[] = $row['cantidad'];
}

// Gráfico 2: stock total por categoría
$stock_query = $conexion->query("SELECT categoria, SUM(stock) AS total_stock FROM productos GROUP BY categoria");
$stock_categorias = [];
$stock_valores = [];
while ($row = $stock_query->fetch_assoc()) {
    $stock_categorias[] = $row['categoria'];
    $stock_valores[] = $row['total_stock'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Listado de Productos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="bg-light">
  <div class="container py-5" id="contenido-productos">
    <h1 class="mb-4 text-center">📊 Resumen de Productos</h1>

    <div class="mb-3 d-flex justify-content-between">
      <a href="index.php" class="btn btn-secondary">← Volver al inventario</a>
      <button class="btn btn-outline-danger" id="exportarPDF">📄 Exportar vista a PDF</button>
    </div>

    <div class="row g-3">
      <div class="col-md-6 col-lg-4">
        <div class="card border-primary shadow-sm">
          <div class="card-body">
            <h5 class="card-title">📦 Total de productos</h5>
            <p class="card-text display-6 text-primary"><?= $total ?></p>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card border-success shadow-sm">
          <div class="card-body">
            <h5 class="card-title">✅ Disponibles</h5>
            <p class="card-text display-6 text-success"><?= $disponibles ?></p>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card border-danger shadow-sm">
          <div class="card-body">
            <h5 class="card-title">❌ Agotados</h5>
            <p class="card-text display-6 text-danger"><?= $agotados ?></p>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card border-info shadow-sm">
          <div class="card-body">
            <h5 class="card-title">🏷️ Categoría más usada</h5>
            <p class="card-text fs-5"><?= htmlspecialchars($categoria_popular) ?></p>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card border-warning shadow-sm">
          <div class="card-body">
            <h5 class="card-title">🆕 Producto más reciente</h5>
            <p class="card-text fs-5"><?= htmlspecialchars($producto_reciente) ?></p>
          </div>
        </div>
      </div>
    </div>

    <hr class="my-5">

    <!-- Gráfico 1 -->
    <h3 class="mb-4 text-center">📈 Productos por categoría</h3>
    <div class="card p-4 mb-5 shadow-sm">
      <canvas id="grafico1" height="100"></canvas>
    </div>

    <!-- Gráfico 2 -->
    <h3 class="mb-4 text-center">📦 Stock total por categoría</h3>
    <div class="card p-4 shadow-sm">
      <canvas id="grafico2" height="100"></canvas>
    </div>
  </div>

<script>
  const ctx1 = document.getElementById('grafico1').getContext('2d');
  new Chart(ctx1, {
    type: 'bar',
    data: {
      labels: <?= json_encode($categorias) ?>,
      datasets: [{
        label: 'Cantidad de productos',
        data: <?= json_encode($valores) ?>,
        backgroundColor: 'rgba(54, 162, 235, 0.6)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: { y: { beginAtZero: true } }
    }
  });

  const ctx2 = document.getElementById('grafico2').getContext('2d');
  new Chart(ctx2, {
    type: 'pie',
    data: {
      labels: <?= json_encode($stock_categorias) ?>,
      datasets: [{
        label: 'Stock total',
        data: <?= json_encode($stock_valores) ?>,
        backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#dc3545', '#6f42c1', '#20c997']
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  });

  // Exportar a PDF
  document.getElementById('exportarPDF').addEventListener('click', () => {
    import('jspdf').then(jsPDF => {
      html2canvas(document.getElementById('contenido-productos')).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF.jsPDF('p', 'mm', 'a4');
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
        pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
        pdf.save("resumen_productos.pdf");
      });
    });
  });
</script>
</body>
</html>
