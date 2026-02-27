<?php 
require_once "conexion.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    header("Location: inventario.php");
    exit;
}

$stmt = $conexion->prepare("SELECT * FROM inventario WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

$equipo = $resultado->fetch_assoc();
$stmt->close();

if (!$equipo) {
    header("Location: inventario.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Detalle Inventario</title>
<link rel="stylesheet" href="estilo.css">
</head>
<body>

<div class="card-form">
    <h2>Detalle del equipo</h2>

    <p><strong>HOST:</strong> <?= htmlspecialchars($equipo['host']) ?></p>
    <p><strong>IP:</strong> <?= htmlspecialchars($equipo['ip']) ?></p>
    <p><strong>Referencia:</strong> <?= htmlspecialchars($equipo['ref']) ?></p>
    <p><strong>Ubicación:</strong> <?= htmlspecialchars($equipo['ubicacion']) ?></p>
    <p><strong>Activo Fijo:</strong> <?= htmlspecialchars($equipo['activo_fijo']) ?></p>
    <p><strong>Modelo:</strong> <?= htmlspecialchars($equipo['modelo']) ?></p>
    <p><strong>CPU:</strong> <?= htmlspecialchars($equipo['cpu']) ?></p>
    <p><strong>RAM:</strong> <?= htmlspecialchars($equipo['ram']) ?></p>
    <p><strong>N° Serie:</strong> <?= htmlspecialchars($equipo['numero_serie']) ?></p>
    <p><strong>Huellero:</strong> <?= htmlspecialchars($equipo['huellero']) ?></p>

    <div class="acciones-form">
        <a href="inventario.php" class="btn">Volver</a>
        <a href="editar_inventario.php?id=<?= $equipo['id'] ?>" class="btn-pill">Editar</a>
    </div>
</div>

</body>
</html>