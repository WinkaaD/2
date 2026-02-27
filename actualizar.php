<?php
require_once "conexion.php";

if (!isset($_POST['anexo'])) {
    header("Location: index.php?msg=error");
    exit;
}

$anexo = $_POST['anexo'];
$telefono = $_POST['telefono'];
$ubicacion = $_POST['ubicacion'];

/* 1️⃣ Obtener datos actuales antes de modificar */
$stmtOld = $conexion->prepare("SELECT * FROM anexos WHERE anexo = ?");
$stmtOld->bind_param("s", $anexo);
$stmtOld->execute();
$resultOld = $stmtOld->get_result();

if ($resultOld->num_rows === 0) {
    header("Location: index.php?msg=error");
    exit;
}

$datoViejo = $resultOld->fetch_assoc();

/* 2️⃣ Guardar versión anterior en historial */
$stmtHist = $conexion->prepare(
    "INSERT INTO historial_anexos
    (anexo, telefono, ubicacion, fecha, restaurado)
    VALUES (?, ?, ?, NOW(), 0)"
);

$stmtHist->bind_param(
    "sss",
    $datoViejo['anexo'],
    $datoViejo['telefono'],
    $datoViejo['ubicacion']
);

$stmtHist->execute();

/* 3️⃣ Ahora sí hacer el UPDATE */
$stmtUpdate = $conexion->prepare(
    "UPDATE anexos
     SET telefono = ?, ubicacion = ?
     WHERE anexo = ?"
);

$stmtUpdate->bind_param("sss", $telefono, $ubicacion, $anexo);

if ($stmtUpdate->execute()) {
    header("Location: index.php?msg=actualizado");
} else {
    header("Location: index.php?msg=error");
}

exit;
$anexo = $_POST['anexo'];
if (!is_numeric($anexo) || strlen($anexo) > 5) {
    $_SESSION['error'] = "El anexo debe ser un número de máximo 5 dígitos.";
    header("Location: nuevo.php");
    exit();
}