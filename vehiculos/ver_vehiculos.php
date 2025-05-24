<?php
include("../conexion.php");
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID inválido.";
    exit();
}

$id = (int)$_GET['id'];

$stmt = mysqli_prepare($connec, "SELECT marca, modelo, descripcion, precio, imagen FROM vehiculos WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$vehiculo = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if (!$vehiculo) {
    echo "Vehículo no encontrado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del vehiculo</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/barra_lateral.css">
    <link rel="stylesheet" href="../css/vehiculos_css/ver_vehiculos.css">
    
</head>
<div class="wrapper">
        <?php 
        include('../barras/navbar.php');
        include('../barras/barra_lateral.php');
        ?>
    <body>
    <div class="container">
        <h2>Detalles del Vehículo</h2>
        <div class="vehiculo-info">
            <label>Marca:</label>
            <p><?= htmlspecialchars($vehiculo['marca']) ?></p>

            <label>Modelo:</label>
            <p><?= htmlspecialchars($vehiculo['modelo']) ?></p>

            <label>Descripción:</label>
            <p><?= htmlspecialchars($vehiculo['descripcion']) ?></p>

            <label>Precio:</label>
            <p>$<?= number_format($vehiculo['precio'], 2) ?></p>


            <?php if (!empty($vehiculo['imagen']) && file_exists("../Imagen/" . $vehiculo['imagen'])): ?>
                <label>Imagen:</label>
                <img src="../Imagen/<?= htmlspecialchars($vehiculo['imagen']) ?>" alt="Imagen del vehículo">
            <?php else: ?>
                <p><em>Sin imagen disponible</em></p>
            <?php endif; ?>
        </div>
        <div class="btn-volver">
            <a href="vehiculos.php">Volver</a>
        </div>
    </div>
</body>
</html>
