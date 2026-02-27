<?php
require_once "anexos.php";

$resultado = obtenerAnexos(); 
$anexos = $resultado->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Anexos</title>
<link rel="stylesheet" href="estilo.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>

<div class="top-container">
  <h2>Listado de Anexos</h2>

  <div class="acciones">
    <a href="nuevo.php" class="btn-agregar">Agregar nuevo</a>

    <input
      type="text"
      id="busqueda"
      placeholder="Buscar anexo, teléfono o ubicación..."
    >

    <button class="icon-button" onclick="location.href='historial.php'">
      <i class="fa fa-history"></i>
    </button>
  </div>
</div>

<!-- MENSAJES -->
<?php if (isset($_GET['msg']) && $_GET['msg'] === 'eliminado'): ?>
    <div class="alert-success">
         Anexo eliminado correctamente
    </div>
<?php endif; ?>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'error'): ?>
    <div class="alert-error">
         Error al eliminar el anexo
    </div>
<?php endif; ?>

<!-- TABLA -->
<table id="tablaAnexos">

<thead>
<tr>
  <th>Anexo</th>
  <th>Teléfono</th>
  <th>Ubicación</th>
  <th>Acciones</th>
</tr>
</thead>

<tbody>
<?php if (!empty($anexos)): ?>
  <?php foreach ($anexos as $a): ?>
    <tr>
      <td><?= htmlspecialchars($a['anexo']) ?></td>
      <td><?= htmlspecialchars($a['telefono']) ?></td>
      <td><?= htmlspecialchars($a['ubicacion']) ?></td>

      <td class="acciones-tabla">
        <a href="editar.php?anexo=<?= urlencode($a['anexo']) ?>"
           class="btn btn-success">
           Editar
        </a>

        <form action="eliminar.php" method="POST" style="display:inline;">
            <input type="hidden" name="anexo"
                   value="<?= htmlspecialchars($a['anexo']) ?>">

            <button class="btn btn-danger">
                Eliminar
            </button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="4">No hay anexos registrados.</td>
    </tr>
<?php endif; ?>
</tbody>

</table>

<script>
document.getElementById('busqueda').addEventListener('keyup', function () {
  const texto = this.value.toLowerCase();
  const filas = document.querySelectorAll('#tablaAnexos tbody tr');

  filas.forEach(fila => {
    const contenido = fila.textContent.toLowerCase();
    fila.style.display = contenido.includes(texto) ? '' : 'none';
  });
});
</script>

</body>
</html>