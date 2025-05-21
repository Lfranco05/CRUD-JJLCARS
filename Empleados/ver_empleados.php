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

$id = mysqli_real_escape_string($connec, $_GET['id']);
$stmt = mysqli_prepare($connec, "SELECT * FROM empleados WHERE id = ?");
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
    <title>Detalles del Empleado</title>
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
                <h2 class="detalles-titulo"><i class="fas fa-id-badge"></i> Detalles del Empleado</h2>

                <div class="detalle-item">
                    <label><i class="fas fa-user"></i> Nombre:</label>
                    <div class="detalle-valor"><?php echo htmlspecialchars($Usuarios['nombre']); ?></div>
                </div>

                <div class="detalle-item">
                    <label><i class="fas fa-user-circle"></i> Usuario:</label>
                    <div class="detalle-valor"><?php echo htmlspecialchars($Usuarios['usuario']); ?></div>
                </div>

                <div class="detalle-item">
                    <label><i class="fas fa-user-tag"></i> Tipo de Usuario:</label>
                    <div class="detalle-valor"><?php echo htmlspecialchars($Usuarios['TipoUsuario']); ?></div>
                </div>

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
