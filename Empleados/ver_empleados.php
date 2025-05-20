<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: empleados.php");
    exit();
}

// Obtener los datos del usuario
$id = mysqli_real_escape_string($connec, $_GET['id']);
$stmt = mysqli_prepare($connec, "SELECT * FROM Usuarios WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$Usuarios = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if (!$Usuarios) {
    header("Location: empleados.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../css/barra_lateral.css">
    <link rel="stylesheet" type="text/css" href="../css/ver_empleados.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php 
        include('../barras/navbar.php');
        include('../barras/barra_lateral.php');
        ?>

        <div class="main-container">
            <div class="detalles-container">
                <h2 class="detalles-titulo">Detalles del empleado</h2>
                <div class="detalle-valor"><?php echo htmlspecialchars($Usuarios['Nombre']); ?></div>

                <div class="detalle-valor"><?php echo htmlspecialchars($Usuarios['Usuario']); ?></div>

                <div class="detalle-valor"><?php echo htmlspecialchars($Usuarios['TipoUsuario']); ?></div>

                <div class="botones-accion">
                    <a href="empleados.php" class="btn-volver">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>