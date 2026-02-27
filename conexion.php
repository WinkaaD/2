<?php

$host = "localhost";
$usuario = "root";
$password = "";
$anexos = "anexos"; 

$conexion = new mysqli($host, $usuario, $password, $anexos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

/* Opcional pero recomendado */
$conexion->set_charset("utf8mb4");

?> 