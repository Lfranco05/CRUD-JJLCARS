<?php
session_start();
include("../conexion.php");

// Verificación de sesión
if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: login.php");
    exit();
}

// Verificar si se proporciona un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID de cita no válido.";
    exit();
}

$id = intval($_GET['id']);

// Consulta para obtener los detalles de la cita
$stmt = mysqli_prepare($connec, "SELECT nombre, tipoCita, tipoCompra, precio, status, fecha, hora FROM citas WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $nombre = $row['nombre'];
    $tipoCita = $row['tipoCita'];
    $tipoCompra = $row['tipoCompra'];
    $precio = $row['precio'];
    $status = $row['status'];
    $fecha = $row['fecha'];
    $hora = $row['hora'];
} else {
    echo "Cita no encontrada.";
    exit();
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles de la Cita</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/barra_lateral.css">
    <link rel="stylesheet" href="../../css/ventas_css/ver_cita.css">
</head>
<body>
    <div class="wrapper">
        <?php include('../barras/navbar.php'); ?>
        <?php include('../barras/barra_lateral.php'); ?>

        <div class="main-container">
            <h1>Detalles de la Cita</h1>
            <div class="detalle-cita">
                <p><strong>Nombre del cliente:</strong> <?php echo htmlspecialchars($nombre); ?></p>
                <p><strong>Tipo de Cita:</strong> <?php echo htmlspecialchars($tipoCita); ?></p>
                <p><strong>Tipo de Compra:</strong> <?php echo htmlspecialchars($tipoCompra); ?></p>
                <p><strong>Precio:</strong> $<?php echo number_format($precio, 2); ?></p>
                <p><strong>Fecha:</strong> <?php echo htmlspecialchars($fecha); ?></p>
                <p><strong>Hora:</strong> <?php echo htmlspecialchars($hora); ?></p>
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($status); ?></p>
                <a href="ventas.php" class="btn-volver">Volver</a>
            </div>
        </div>
    </div>
</body>
</html>
