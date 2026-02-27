<?php
session_start();
require_once "anexos.php";

$respuesta = crearAnexo(
    $_POST['anexo'] ?? '',
    $_POST['telefono'] ?? '',
    $_POST['ubicacion'] ?? ''
);

if ($respuesta !== true) {
    $_SESSION['error'] = $respuesta;
    header("Location: nuevo.php");
    exit;
}

$_SESSION['exito'] = "Anexo creado correctamente";
header("Location: index.php");
exit;