<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: clientes.php");
    exit();
}

$id = mysqli_real_escape_string($connec, $_GET['id']);


$stmt = mysqli_prepare($connec, "DELETE FROM clientes WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
$resultado = mysqli_stmt_execute($stmt);

if ($resultado) {
    echo "<script>alert('Cliente elimando de manera exitosa de la base'); location.href='vehiculos.php';</script>";
} else {
    echo "<script>alert('Error al eliminar cliente de la base " . mysqli_error($connec) . "'); location.href='clientes.php';</script>";
}

mysqli_stmt_close($stmt);
exit();
?>