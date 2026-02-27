<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo Anexo</title>
<link rel="stylesheet" href="estilo.css">
</head>
<body>

<h2>Nuevo Anexo</h2>

<?php if (isset($_SESSION['error'])): ?>
  <p style="color:red"><?= $_SESSION['error'] ?></p>
  <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<form action="guardar.php" method="POST">

  <label>Anexo:</label><br>
<input type="text" 
       name="anexo" 
       id="anexo" 
       maxlength="5" 
       oninput="soloNumeros(this)" 
       pattern="\d*" 
       required><br><br>

  <label>Teléfono:</label><br>
  <input type="text" 
		name="telefono" 
		id="telefono"
		maxlength="9"
		oninput="soloNumeros(this)" 
       pattern="\d*" 
	   required><br><br>

  <label>Ubicación:</label><br>
  <input type="text" name="ubicacion" required><br><br>

      <button type="submit" class="btn btn-success">
        Guardar cambios
      </button>

  <a href="index.php" class="btn btn-success btn-secundario">
        Volver
		</a>
</form>
<script>
function soloNumeros(input) {
   
    input.value = input.value.replace(/[^0-9]/g, '');
}
</script>
</body>
</html>