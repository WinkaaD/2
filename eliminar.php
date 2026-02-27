<?php
require_once "anexos.php";

if (!isset($_POST['anexo'])) {
    header("Location: index.php?msg=error");
    exit;
}

$anexo = $_POST['anexo'];

if (eliminarAnexo($anexo)) {
    header("Location: index.php?msg=eliminado");
} else {
    header("Location: index.php?msg=error");
}
exit;