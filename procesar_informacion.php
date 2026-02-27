<?php

require_once "conexion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: importar_inventario.php");
    exit;
}

if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== 0) {
    die("No se seleccionó ningún archivo válido");
}

$archivoTmp = $_FILES['archivo']['tmp_name'];

if (($handle = fopen($archivoTmp, "r")) === false) {
    die("No se pudo abrir el archivo");
}

/* PREPARAR INSERT INVENTARIO */
$stmt = $conexion->prepare("
    INSERT INTO inventario.inventario (
        torre, piso, ref, ubicacion, host, ip, tipo,
        cod_anydesk, modelo, cpu, ram,
        numero_serie, numero_producto, activo_fijo, huellero
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

/* PREPARAR INSERT ERRORES */
$stmtError = $conexion->prepare("
    INSERT INTO inventario_errores (
        torre, piso, ref, ubicacion, host, ip, tipo,
        cod_anydesk, modelo, cpu, ram,
        numero_serie, numero_producto, activo_fijo, huellero, motivo
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt || !$stmtError) {
    die("Error preparando consultas: " . $conexion->error);
}

$linea = 0;

while (($datos = fgetcsv($handle, 0, ";")) !== false) {

    // Saltar encabezado
    if ($linea === 0) {
        $linea++;
        continue;
    }

    $torre           = trim($datos[0] ?? '');
    $piso            = trim($datos[1] ?? '');
    $ref             = trim($datos[2] ?? '');
    $ubicacion       = trim($datos[3] ?? '');
    $host            = trim($datos[4] ?? '');
    $ip              = trim($datos[5] ?? '');
    $tipo            = trim($datos[6] ?? '');
    $cod_anydesk     = trim($datos[7] ?? '');
    $modelo          = trim($datos[8] ?? '');
    $cpu             = trim($datos[9] ?? '');
    $ram             = trim($datos[10] ?? '');
    $numero_serie    = trim($datos[11] ?? '');
    $numero_producto = trim($datos[12] ?? '');
    $activo_fijo     = trim($datos[13] ?? '');
    $huellero        = trim($datos[14] ?? '');

    $motivo = '';

    /* VALIDAR DATOS INCOMPLETOS */
    if ($host === '' || $ip === '') {
        $motivo .= "Datos incompletos. ";
    }

    /* VALIDAR DUPLICADOS */
    $check = $conexion->prepare("
        SELECT id FROM inventario.inventario
        WHERE host = ?
           OR ip = ?
           OR numero_serie = ?
           OR activo_fijo = ?
        LIMIT 1
    ");

    if (!$check) {
        die("Error preparando validación: " . $conexion->error);
    }

    $check->bind_param("ssss", $host, $ip, $numero_serie, $activo_fijo);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $motivo .= "Duplicado por clave única.";
    }

    $check->close();

    /* INSERTAR SEGÚN VALIDACIÓN */
    if ($motivo === '') {

        $stmt->bind_param(
            "sssssssssssssss",
            $torre, $piso, $ref, $ubicacion, $host, $ip, $tipo,
            $cod_anydesk, $modelo, $cpu, $ram,
            $numero_serie, $numero_producto, $activo_fijo, $huellero
        );

        if (!$stmt->execute()) {
            die("Error insertando inventario: " . $stmt->error);
        }

    } else {

        $stmtError->bind_param(
            "ssssssssssssssss",
            $torre, $piso, $ref, $ubicacion, $host, $ip, $tipo,
            $cod_anydesk, $modelo, $cpu, $ram,
            $numero_serie, $numero_producto, $activo_fijo, $huellero, $motivo
        );

        if (!$stmtError->execute()) {
            die("Error insertando en errores: " . $stmtError->error);
        }
    }
}

fclose($handle);

$stmt->close();
$stmtError->close();

header("Location: inventario.php");
exit;