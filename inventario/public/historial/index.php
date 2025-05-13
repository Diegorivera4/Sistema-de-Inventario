<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../admin/login.php");
    exit();
}

include '../../db/conexion.php';

// Filtros
$tipo = $_GET['tipo'] ?? '';
$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';

$where = [];
if ($tipo !== '') $where[] = "h.tipo_movimiento = '$tipo'";
if ($desde !== '') $where[] = "DATE(h.fecha) >= '$desde'";
if ($hasta !== '') $where[] = "DATE(h.fecha) <= '$hasta'";
$condicion = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

$historial = $conexion->query("
  SELECT h.*, p.nombre AS producto 
  FROM historial h 
  LEFT JOIN productos p ON h.producto_id = p.id 
  $condicion
  ORDER BY h.fecha DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial de Movimientos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-5 animate-fade-in" id="historial-contenido">
  <h1 class="text-center mb-4">📋 Historial de Movimientos</h1>

  <div class="d-flex justify-content-between mb-3">
    <a href="../index.php" class="btn btn-secondary">← Volver al inventario</a>
    <button class="btn btn-outline-danger" id="exportarPDF">📄 Exportar vista a PDF</button>
  </div>

  <!-- Filtros -->
  <form method="GET" class="row g-3 mb-3">
    <div class="col-md-3">
      <label class="form-label">Tipo de movimiento</label>
      <select name="tipo" class="form-select">
        <option value="">Todos</option>
        <option value="AGREGADO" <?= $tipo === 'AGREGADO' ? 'selected' : '' ?>>AGREGADO</option>
        <option value="EDITADO" <?= $tipo === 'EDITADO' ? 'selected' : '' ?>>EDITADO</option>
        <option value="ELIMINADO" <?= $tipo === 'ELIMINADO' ? 'selected' : '' ?>>ELIMINADO</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Desde</label>
      <input type="date" name="desde" class="form-control" value="<?= htmlspecialchars($desde) ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Hasta</label>
      <input type="date" name="hasta" class="form-control" value="<?= htmlspecialchars($hasta) ?>">
    </div>
    <div class="col-md-3 d-flex align-items-end">
      <button type="submit" class="btn btn-primary w-100">Filtrar</button>
    </div>
  </form>

  <!-- Exportar a Excel/PDF -->
  <div class="mb-3">
    <a href="exportar_historial_excel.php?tipo=<?= urlencode($tipo) ?>&desde=<?= urlencode($desde) ?>&hasta=<?= urlencode($hasta) ?>" class="btn btn-outline-success btn-sm" target="_blank">📥 Exportar a Excel</a>
    <a href="exportar_historial_pdf.php?tipo=<?= urlencode($tipo) ?>&desde=<?= urlencode($desde) ?>&hasta=<?= urlencode($hasta) ?>" class="btn btn-outline-dark btn-sm" target="_blank">📄 Exportar tabla en PDF</a>
  </div>

  <!-- Tabla -->
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>Fecha</th>
          <th>Producto</th>
          <th>Movimiento</th>
          <th>Usuario</th>
          <th>Descripción</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($historial->num_rows > 0): ?>
          <?php while ($fila = $historial->fetch_assoc()): ?>
            <tr>
              <td><?= date("d/m/Y H:i", strtotime($fila['fecha'])) ?></td>
              <td><?= htmlspecialchars($fila['producto']) ?></td>
              <td>
                <span class="badge <?= 
                    $fila['tipo_movimiento'] === 'AGREGADO' ? 'bg-success' : (
                    $fila['tipo_movimiento'] === 'EDITADO' ? 'bg-warning text-dark' :
                    'bg-danger') ?>">
                  <?= $fila['tipo_movimiento'] ?>
                </span>
              </td>
              <td><?= htmlspecialchars($fila['usuario']) ?></td>
              <td><?= htmlspecialchars($fila['descripcion']) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="5" class="text-center text-muted">No hay resultados con los filtros seleccionados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Script: exportar a PDF -->
<script>
document.getElementById('exportarPDF').addEventListener('click', () => {
  import('jspdf').then(jsPDF => {
    html2canvas(document.getElementById('historial-contenido')).then(canvas => {
      const imgData = canvas.toDataURL('image/png');
      const pdf = new jsPDF.jsPDF('p', 'mm', 'a4');
      const pdfWidth = pdf.internal.pageSize.getWidth();
      const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
      pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
      pdf.save("historial_movimientos.pdf");
    });
  });
});
</script>
</body>
</html>
