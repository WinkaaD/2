<?php
require_once "conexion.php";

$error = "";
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: inventario.php");
    exit;
}

/* 1️⃣ Obtener datos actuales */
$stmt = $conexion->prepare("SELECT * FROM inventario WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$equipo = $resultado->fetch_assoc();

if (!$equipo) {
    header("Location: inventario.php");
    exit;
}

/* 2️⃣ Guardar cambios */
if ($_POST) {
    try {
        $stmt = $conexion->prepare("
            UPDATE inventario SET
                torre = ?, piso = ?, ref = ?, ubicacion = ?,
                host = ?, ip = ?, tipo = ?, cod_anydesk = ?,
                modelo = ?, cpu = ?, ram = ?,
                numero_serie = ?, numero_producto = ?,
                activo_fijo = ?, huellero = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "sssssssssssssssi",
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
            $_POST['huellero'],
            $id
        );

        $stmt->execute();
        header("Location: inventario.php");
        exit;

    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $error = "⚠️ Ya existe otro equipo con esos datos (IP, Host, Activo fijo, Serie o Huellero).";
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
<title>Editar Equipo</title>
<link rel="stylesheet" href="estilo.css">
</head>
<body>

<h2>Editar Equipo</h2>

<?php if ($error): ?>
    <p style="color:red; font-weight:bold;">
        <?= $error ?>
    </p>
<?php endif; ?>

<form method="POST">

<input type="text" name="torre" value="<?= $equipo['torre'] ?>">
<input type="text" name="piso" value="<?= $equipo['piso'] ?>">
<input type="text" name="ref" value="<?= $equipo['ref'] ?>">
<input type="text" name="ubicacion" value="<?= $equipo['ubicacion'] ?>">

<input type="text" name="host" value="<?= $equipo['host'] ?>" required>
<input type="text" name="ip" value="<?= $equipo['ip'] ?>" required>

<input type="text" name="tipo" value="<?= $equipo['tipo'] ?>">
<input type="text" name="cod_anydesk" value="<?= $equipo['cod_anydesk'] ?>">

<input type="text" name="modelo" value="<?= $equipo['modelo'] ?>">
<input type="text" name="cpu" value="<?= $equipo['cpu'] ?>">
<input type="text" name="ram" value="<?= $equipo['ram'] ?>">

<input type="text" name="numero_serie" value="<?= $equipo['numero_serie'] ?>">
<input type="text" name="numero_producto" value="<?= $equipo['numero_producto'] ?>">
<input type="text" name="activo_fijo" value="<?= $equipo['activo_fijo'] ?>">
<input type="text" name="huellero" value="<?= $equipo['huellero'] ?>">

<br><br>

<button type="submit" class="btn">Actualizar</button>
<a href="inventario.php" class="btn">Volver</a>

</form>

</body>
</html>