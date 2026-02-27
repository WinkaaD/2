<?php
session_start();
require_once "anexos.php";

$anexo = $_GET['anexo'] ?? null;

if (!$anexo) {
    $_SESSION['error'] = 'Anexo no especificado';
    header('Location: index.php');
    exit;
}

$dato = obtenerAnexo($anexo);

if (!$dato) {
    $_SESSION['error'] = 'Anexo no encontrado';
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Anexo</title>
<link rel="stylesheet" href="estilo.css">
</head>

<body>

  <h2>Editar Anexo</h2>

  <?php if (isset($_SESSION['error'])): ?>
    <p style="color:red"><?= $_SESSION['error'] ?></p>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <form action="actualizar.php" method="POST">

    <div class="campo">
      <label>Anexo</label>
      <input type="text"
             name="anexo"
             value="<?= htmlspecialchars($dato['anexo']) ?>"
             readonly>
    </div>

    <div class="campo">
      <label>Teléfono</label>
      <input type="text"
             name="telefono"
             value="<?= htmlspecialchars($dato['telefono']) ?>"
             maxlength="9">
    </div>

    <div class="campo">
      <label>Ubicación</label>
      <input type="text"
             name="ubicacion"
             value="<?= htmlspecialchars($dato['ubicacion']) ?>">
    </div>

    <div class="acciones-form">
      <button type="submit" class="btn btn-success">
        Guardar cambios
      </button>

      <a href="index.php" class="btn btn-success btn-secundario">
        Volver
      </a>
    </div>

  </form>

</body>
</html>