<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ventas.php");
    exit();
}

$id = mysqli_real_escape_string($connec, $_GET['id']);


$stmt = mysqli_prepare($connec, "DELETE FROM citas WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
$resultado = mysqli_stmt_execute($stmt);

if ($resultado) {
    echo "<script>alert('Cita eliminada de manera exitosa'); location.href='ventas.php';</script>";
} else {
    echo "<script>alert('Error al eliminar cita " . mysqli_error($connec) . "'); location.href='ventas.php';</script>";
}

mysqli_stmt_close($stmt);
exit();
?>