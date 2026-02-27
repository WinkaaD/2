<?php
require_once "conexion.php";

$resultado = $conexion->query("
    SELECT * FROM historial_anexos
    ORDER BY fecha DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historial de Anexos</title>
<link rel="stylesheet" href="estilo.css">

</head>

<body>

<h2>Historial de Anexos</h2>

<div class="top-container">
  <a href="index.php" class="btn btn-success">⬅ Volver</a>
  <input type="text" id="busqueda" placeholder="Buscar anexo, teléfono o ubicación...">
</div>

<!-- MENSAJES -->
<?php if (isset($_GET['msg']) && $_GET['msg'] === 'restaurado'): ?>
  <div class="alert-success">
    ✔ Anexo restaurado correctamente
  </div>
<?php endif; ?>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'error'): ?>
  <div class="alert-error">
    ✖ Error al restaurar el anexo
  </div>
<?php endif; ?>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'existe'): ?>
  <div class="alert-error">
    ⚠ El anexo ya existe en el sistema
  </div>
<?php endif; ?>

<table id="tablaHistorial">

<thead>
<tr>
  <th>Anexo</th>
  <th>Teléfono</th>
  <th>Ubicación</th>
  <th>Fecha</th>
  <th>Acción</th>
</tr>
</thead>

<tbody>
<?php while ($row = $resultado->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($row['anexo']) ?></td>
  <td><?= htmlspecialchars($row['telefono']) ?></td>
  <td><?= htmlspecialchars($row['ubicacion']) ?></td>
  <td><?= htmlspecialchars($row['fecha']) ?></td>
  <td>
    <?php if ($row['restaurado'] == 0): ?>
<a href="restaurar.php?id=<?= $row['id'] ?>"
   onclick="return confirm('¿Restaurar este registro?')">
   Restaurar
</a>
    <?php else: ?>
      ✔ Restaurado
    <?php endif; ?>
  </td>
</tr>
<?php endwhile; ?>
</tbody>

</table>

<script>
document.getElementById('busqueda').addEventListener('keyup', function () {
  const texto = this.value.toLowerCase();
  const filas = document.querySelectorAll('#tablaHistorial tbody tr');

  filas.forEach(fila => {
    const contenido = fila.textContent.toLowerCase();
    fila.style.display = contenido.includes(texto) ? '' : 'none';
  });
});
</script>

</body>
</html>