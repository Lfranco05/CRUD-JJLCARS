<?php
$servidor = "localhost";
$usuario = "root"; 
$clave = "";       
$base_datos = "jjlcars"; // Esta es la base de datos que estamos usando amigos, porfis

$connec = mysqli_connect($servidor, $usuario, $clave, $base_datos);

if (!$connec) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>