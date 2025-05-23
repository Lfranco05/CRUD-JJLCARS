<?php
session_start();
include("../conexion.php");

// Seguridad para no que no se salten el login
if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: login.php");
    exit();
}

// valida id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID de cliente inválido'); window.location.href = 'clientes.php';</script>";
    exit();
}

$id = (int) $_GET['id'];


$stmt = mysqli_prepare($connec, "SELECT id, nombre, usuario, correo, telefono FROM clientes WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Cliente no encontrado'); window.location.href = 'clientes.php';</script>";
    exit();
}

$cliente = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Cliente</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/barra_lateral.css">
    <link rel="stylesheet" href="../css/clientes_css/ver_clientes.css">
</head>
<body>
    <div class="wrapper">
        <?php include('../barras/navbar.php'); ?>
        <?php include('../barras/barra_lateral.php'); ?>

        <div class="main-container">
            <div class="details-card">
                <h2>Detalles del Cliente</h2>
                <div class="detail-item"><strong>ID:</strong> <?= $cliente['id'] ?></div>
                <div class="detail-item"><strong>Nombre:</strong> <?= htmlspecialchars($cliente['nombre']) ?></div>
                <div class="detail-item"><strong>Usuario:</strong> <?= htmlspecialchars($cliente['usuario']) ?></div>
                <div class="detail-item"><strong>Correo:</strong> <?= htmlspecialchars($cliente['correo']) ?></div>
                <div class="detail-item"><strong>Teléfono:</strong> <?= htmlspecialchars($cliente['telefono']) ?></div>

                <div class="actions">
                    <a href="clientes.php" class="btn-back">Volver</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
