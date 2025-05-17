<?php
$servidor = "localhost";
$usuario = "root"; // Cambia según tu configuración
$clave = "";       // Cambia según tu configuración
$base_datos = "uspg";

$connec = mysqli_connect($servidor, $usuario, $clave, $base_datos);

if (!$connec) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>