<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: login.php");
    exit();
}

// Validar que se recibió el ID y el nuevo estado
if (isset($_POST['id']) && isset($_POST['nuevo_estado'])) {
    $id = intval($_POST['id']);
    $nuevo_estado = $_POST['nuevo_estado'];

    // Validar que el estado es válido
    $estados_validos = ['Pendiente', 'Aprobada', 'Cancelada'];

    if (in_array($nuevo_estado, $estados_validos)) {
        $stmt = mysqli_prepare($connec, "UPDATE citas SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $nuevo_estado, $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: ventas.php?msg=estado_actualizado");
            exit();
        } else {
            $error = "Error al actualizar el estado.";
        }
    } else {
        $error = "Estado no válido.";
    }
} else {
    $error = "Faltan datos requeridos.";
}

// Si algo falla, redirigir con mensaje de error
header("Location: ventas.php?error=" . urlencode($error));
exit();
