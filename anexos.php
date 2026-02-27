<?php
require_once "conexion.php";

function obtenerAnexos() {
    global $conexion;
    return $conexion->query("SELECT * FROM anexos ORDER BY anexo");
}

function obtenerAnexo($anexo) {
    global $conexion;
    $stmt = $conexion->prepare("SELECT * FROM anexos WHERE anexo = ?");
    $stmt->bind_param("s", $anexo);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function crearAnexo($anexo, $telefono, $ubicacion) {
    global $conexion;

    $anexo     = trim($anexo);
    $telefono  = trim($telefono);
    $ubicacion = trim($ubicacion);

    if ($anexo === "" || $telefono === "" || $ubicacion === "") {
        return "❌ Todos los campos son obligatorios";
    }

    if (!ctype_digit($anexo) || strlen($anexo) !== 5) {
        return "❌ El anexo debe tener exactamente 5 números";
    }

    if (!ctype_digit($telefono) || strlen($telefono) > 9) {
        return "❌ El teléfono debe tener máximo 9 números";
    }

    if (strlen($ubicacion) < 3) {
        return "❌ Ubicación demasiado corta";
    }

    $stmt = $conexion->prepare("SELECT anexo FROM anexos WHERE anexo=?");
    $stmt->bind_param("s", $anexo);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        return "❌ El anexo ya existe";
    }

    $stmt = $conexion->prepare(
        "INSERT INTO anexos (anexo, telefono, ubicacion)
         VALUES (?, ?, ?)"
    );
    $stmt->bind_param("sss", $anexo, $telefono, $ubicacion);

    if (!$stmt->execute()) {
        return "❌ Error al guardar";
    }

    return true;
}

function actualizarAnexo($anexo, $telefono, $ubicacion) {
    global $conexion;

    if (!ctype_digit($anexo) || strlen($anexo) !== 5) {
        return "❌ Anexo inválido";
    }

    if (!ctype_digit($telefono) || strlen($telefono) > 9) {
        return "❌ El teléfono debe tener máximo 9 números";
    }

    if (strlen($ubicacion) < 3) {
        return "❌ Ubicación inválida";
    }

    // Actualizar tabla principal
    $stmt = $conexion->prepare(
        "UPDATE anexos SET telefono=?, ubicacion=? WHERE anexo=?"
    );
    $stmt->bind_param("sss", $telefono, $ubicacion, $anexo);

    if (!$stmt->execute()) {
        return "❌ Error al actualizar";
    }

    // 🔥 GUARDAR EDICIÓN EN HISTORIAL
    $stmtHistorial = $conexion->prepare(
        "INSERT INTO historial_anexos (anexo, telefono, ubicacion, accion)
         VALUES (?, ?, ?, 'Editado')"
    );
    $stmtHistorial->bind_param("sss", $anexo, $telefono, $ubicacion);
    $stmtHistorial->execute();

    return true;
}

function eliminarAnexo($anexo) {
    global $conexion;
    $stmt = $conexion->prepare("DELETE FROM anexos WHERE anexo=?");
    $stmt->bind_param("s", $anexo);
    return $stmt->execute();
}
function restaurarAnexo($anexo) {
    global $conexion;

    // 1️⃣ Obtener el anexo del historial
    $stmt = $conexion->prepare(
        "SELECT * FROM historial_anexos WHERE anexo = ?"
    );
    $stmt->bind_param("s", $anexo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        return false;
    }

    $dato = $resultado->fetch_assoc();

    // 2️⃣ Insertarlo nuevamente en anexos
    $stmt2 = $conexion->prepare(
        "INSERT INTO anexos (anexo, telefono, ubicacion)
         VALUES (?, ?, ?)"
    );
    $stmt2->bind_param(
        "sss",
        $dato['anexo'],
        $dato['telefono'],
        $dato['ubicacion']
    );

    if (!$stmt2->execute()) {
        return false;
    }

    // 3️⃣ Eliminarlo del historial
    $stmt3 = $conexion->prepare(
        "DELETE FROM historial_anexos WHERE anexo = ?"
    );
    $stmt3->bind_param("s", $anexo);
    $stmt3->execute();

    return true;
}