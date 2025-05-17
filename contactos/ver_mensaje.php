<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: contactos.php");
    exit();
}

$id = mysqli_real_escape_string($connec, $_GET['id']);
$sql = "SELECT * FROM contacto WHERE id = ?";
$stmt = mysqli_prepare($connec, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$mensaje = mysqli_fetch_assoc($resultado);

if (!$mensaje) {
    header("Location: contactos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Mensaje - Universidad San Pablo</title>
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../css/barra_lateral.css">
    <link rel="stylesheet" type="text/css" href="../css/ver_mensaje.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php 
        include('../barras/navbar.php');
        include('../barras/barra_lateral.php');
        ?>

        <div class="main-container">
            <div class="mensaje-container">
                <h2>Detalles del Mensaje</h2>
                
                <div class="mensaje-campo">
                    <span class="campo-etiqueta">Nombre:</span>
                    <div class="campo-valor"><?php echo htmlspecialchars($mensaje['nombre']); ?></div>
                </div>

                <div class="mensaje-campo">
                    <span class="campo-etiqueta">Correo:</span>
                    <div class="campo-valor"><?php echo htmlspecialchars($mensaje['correo']); ?></div>
                </div>

                <div class="mensaje-campo">
                    <span class="campo-etiqueta">Teléfono:</span>
                    <div class="campo-valor"><?php echo htmlspecialchars($mensaje['telefono']); ?></div>
                </div>

                <div class="mensaje-campo">
                    <span class="campo-etiqueta">Carrera:</span>
                    <div class="campo-valor"><?php echo htmlspecialchars($mensaje['carrera']); ?></div>
                </div>

                <div class="mensaje-campo">
                    <span class="campo-etiqueta">Mensaje:</span>
                    <div class="campo-valor mensaje-texto"><?php echo nl2br(htmlspecialchars($mensaje['mensaje'])); ?></div>
                </div>

                <div class="mensaje-campo">
                    <span class="campo-etiqueta">Fecha de envío:</span>
                    <div class="campo-valor"><?php echo htmlspecialchars($mensaje['fecha_envio']); ?></div>
                </div>

                <div class="botones-accion">
                    <a href="contactos.php" class="btn-volver">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>