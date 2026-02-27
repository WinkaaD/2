<?php
require_once "conexion.php";

$error = "";

if ($_POST) {
    try {
        $stmt = $conexion->prepare("
            INSERT INTO inventario
            (torre, piso, ref, ubicacion, host, ip, tipo, cod_anydesk,
             modelo, cpu, ram, numero_serie, numero_producto, activo_fijo, huellero)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->bind_param(
            "sssssssssssssss",
            $_POST['torre'],
            $_POST['piso'],
            $_POST['ref'],
            $_POST['ubicacion'],
            $_POST['host'],
            $_POST['ip'],
            $_POST['tipo'],
            $_POST['cod_anydesk'],
            $_POST['modelo'],
            $_POST['cpu'],
            $_POST['ram'],
            $_POST['numero_serie'],
            $_POST['numero_producto'],
            $_POST['activo_fijo'],
            $_POST['huellero']
        );

        $stmt->execute();
        header("Location: inventario.php");
        exit;

    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $error = "⚠️ Ya existe un equipo con esos datos (IP, Host, Activo fijo, Serie o Huellero).";
        } else {
            $error = "Error inesperado: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo Equipo</title>
<link rel="stylesheet" href="estilo.css">
</head>
<body>

<h2>Nuevo Equipo</h2>

<?php if ($error): ?>
    <p style="color:red; font-weight:bold;">
        <?= $error ?>
    </p>
<?php endif; ?>

<form method="POST">

<input type="text" name="torre" placeholder="Torre">
<input type="text" name="piso" placeholder="Piso">
<input type="text" name="ref" placeholder="REF">
<input type="text" name="ubicacion" placeholder="Ubicación">

<input type="text" name="host" placeholder="Host (único)" required>
<input type="text" name="ip" placeholder="IP (única)" required>

<input type="text" name="tipo" placeholder="Tipo">
<input type="text" name="cod_anydesk" placeholder="Código AnyDesk">

<input type="text" name="modelo" placeholder="Modelo">
<input type="text" name="cpu" placeholder="CPU">
<input type="text" name="ram" placeholder="RAM">

<input type="text" name="numero_serie" placeholder="Número de Serie (único)">
<input type="text" name="numero_producto" placeholder="Número de Producto">
<input type="text" name="activo_fijo" placeholder="Activo Fijo (único)">
<input type="text" name="huellero" placeholder="Huellero (único)">

<br><br>

<button type="submit" class="btn">Guardar</button>
<a href="inventario.php" class="btn">Volver</a>

</form>

</body>
</html>
