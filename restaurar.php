<?php
require_once "conexion.php";

if (!isset($_GET['id'])) {
    header("Location: historial.php?msg=error");
    exit;
}

$id = intval($_GET['id']);

/* 1️⃣ Obtener registro del historial */
$stmt = $conexion->prepare(
    "SELECT anexo, telefono, ubicacion
     FROM historial_anexos
     WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: historial.php?msg=error");
    exit;
}

$dato = $resultado->fetch_assoc();

/* 2️⃣ Ver si el anexo existe actualmente */
$stmtCheck = $conexion->prepare(
    "SELECT 1 FROM anexos WHERE anexo = ?"
);
$stmtCheck->bind_param("s", $dato['anexo']);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();

if ($resCheck->num_rows > 0) {

    /* 🔁 Si existe → actualizar */
    $stmtUpdate = $conexion->prepare(
        "UPDATE anexos
         SET telefono = ?, ubicacion = ?
         WHERE anexo = ?"
    );
    $stmtUpdate->bind_param(
        "sss",
        $dato['telefono'],
        $dato['ubicacion'],
        $dato['anexo']
    );
    $stmtUpdate->execute();

} else {

    /* ➕ Si no existe → insertar */
    $stmtInsert = $conexion->prepare(
        "INSERT INTO anexos (anexo, telefono, ubicacion)
         VALUES (?, ?, ?)"
    );
    $stmtInsert->bind_param(
        "sss",
        $dato['anexo'],
        $dato['telefono'],
        $dato['ubicacion']
    );
    $stmtInsert->execute();
}

/* 3️⃣ Volver al historial */
header("Location: historial.php?msg=restaurado");
exit;