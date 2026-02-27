<?php
require_once "conexion.php";

header('Content-Type: application/json');

$id = intval($_POST['id'] ?? 0);
$host = trim($_POST['host'] ?? '');
$ip = trim($_POST['ip'] ?? '');
$activo_fijo = trim($_POST['activo_fijo'] ?? '');

$nuevoError = '';

/* 1️⃣ VALIDACIÓN BÁSICA */
if ($host === '' || $ip === '') {
    $nuevoError = 'Faltan datos obligatorios';
}

/* 2️⃣ VALIDACIÓN DUPLICADOS */
if ($nuevoError === '') {

    $duplicados = [];

    // IP (excluyendo el mismo registro)
    $stmt = $conexion->prepare("
        SELECT id FROM inventario 
        WHERE ip = ? 
        LIMIT 1
    ");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $duplicados[] = 'IP';
    }
    $stmt->close();

    // HOST
    $stmt = $conexion->prepare("
        SELECT id FROM inventario 
        WHERE host = ? 
        LIMIT 1
    ");
    $stmt->bind_param("s", $host);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $duplicados[] = 'Host';
    }
    $stmt->close();

    // ACTIVO FIJO (solo si es válido)
    $activo_fijo_upper = strtoupper($activo_fijo);

    if ($activo_fijo_upper !== '' &&
        $activo_fijo_upper !== 'NO TIENE' &&
        $activo_fijo_upper !== 'SIN ACTIVO FIJO' &&
        $activo_fijo_upper !== 'REVISAR') {

        $stmt = $conexion->prepare("
            SELECT id FROM inventario 
            WHERE activo_fijo = ? 
            LIMIT 1
        ");
        $stmt->bind_param("s", $activo_fijo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $duplicados[] = 'Activo Fijo';
        }

        $stmt->close();
    }

    if (!empty($duplicados)) {
        $nuevoError = 'Duplicado en: ' . implode(', ', $duplicados);
    }
}

/* 3️⃣ ACTUALIZAR inventario_errores */
$stmt = $conexion->prepare("
    UPDATE inventario_errores
    SET host = ?, ip = ?, activo_fijo = ?, motivo = ?
    WHERE id = ?
");
$stmt->bind_param("ssssi", $host, $ip, $activo_fijo, $nuevoError, $id);
$stmt->execute();
$stmt->close();

/* 4️⃣ SI YA NO HAY ERROR → MOVER */
if ($nuevoError === '') {

    // Copiar TODOS los datos desde inventario_errores
    $stmt = $conexion->prepare("
        INSERT INTO inventario (host, ip, activo_fijo)
        SELECT host, ip, activo_fijo
        FROM inventario_errores
        WHERE id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Eliminar de errores
    $stmt = $conexion->prepare("
        DELETE FROM inventario_errores
        WHERE id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['exito' => true]);

} else {

    echo json_encode([
        'exito' => false,
        'mensaje' => $nuevoError
    ]);
}
