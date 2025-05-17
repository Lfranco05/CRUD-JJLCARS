<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($connec, $_GET['id']);
    
    $sql = "DELETE FROM contacto WHERE id = ?";
    $stmt = mysqli_prepare($connec, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: contactos.php");
    } else {
        echo "Error al eliminar el mensaje.";
    }
    
    mysqli_stmt_close($stmt);
} else {
    header("Location: contactos.php");
}
?>