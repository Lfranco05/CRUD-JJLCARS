<?php
session_start();
include("conexion.php");

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: login.php");
    exit();
}

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: estudiantes.php");
    exit();
}

$id = mysqli_real_escape_string($connec, $_GET['id']);

// Usar prepared statement para mayor seguridad
$stmt = mysqli_prepare($connec, "DELETE FROM alumno WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
$resultado = mysqli_stmt_execute($stmt);

if ($resultado) {
    echo "<script>alert('Estudiante eliminado exitosamente'); location.href='estudiantes.php';</script>";
} else {
    echo "<script>alert('Error al eliminar el estudiante: " . mysqli_error($connec) . "'); location.href='estudiantes.php';</script>";
}

mysqli_stmt_close($stmt);
exit();
?>