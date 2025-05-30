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
$sql = "SELECT * FROM citas WHERE id = ?";
$stmt = mysqli_prepare($connec, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$cita = mysqli_fetch_assoc($resultado);

if (!$cita) {
    header("Location: contactos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Cita - JJLCARS</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/barra_lateral.css">
    <link rel="stylesheet" href="../css/contactos_css/ver_mensaje.css">
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
            <h2>Detalles de la Cita</h2>

            <div class="mensaje-campo">
                <span class="campo-etiqueta">Nombre:</span>
                <div class="campo-valor"><?php echo htmlspecialchars($cita['nombre']); ?></div>
            </div>

            <div class="mensaje-campo">
                <span class="campo-etiqueta">Correo:</span>
                <div class="campo-valor"><?php echo htmlspecialchars($cita['correo']); ?></div>
            </div>

            <div class="mensaje-campo">
                <span class="campo-etiqueta">Tipo de Cita:</span>
                <div class="campo-valor"><?php echo htmlspecialchars($cita['tipoCita']); ?></div>
            </div>

            <div class="mensaje-campo">
                <span class="campo-etiqueta">Detalle solicitado:</span>
                <div class="campo-valor"><?php echo htmlspecialchars($cita['tipoCompra']); ?></div>
            </div>

            <?php if (!empty($cita['vehiculo'])): ?>
            <div class="mensaje-campo">
                <span class="campo-etiqueta">Vehículo Seleccionado:</span>
                <div class="campo-valor"><?php echo htmlspecialchars($cita['vehiculo']); ?></div>
            </div>
            <?php endif; ?>

            <div class="mensaje-campo">
                <span class="campo-etiqueta">Precio Estimado:</span>
                <div class="campo-valor">$<?php echo number_format($cita['precio'], 2); ?></div>
            </div>

            <div class="mensaje-campo">
                <span class="campo-etiqueta">Estado:</span>
                <div class="campo-valor"><?php echo htmlspecialchars($cita['status']); ?></div>
            </div>

            <div class="mensaje-campo">
                <span class="campo-etiqueta">Fecha y Hora:</span>
                <div class="campo-valor"><?php echo date('d/m/Y H:i', strtotime($cita['fecha'] . ' ' . $cita['hora'])); ?></div>
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
